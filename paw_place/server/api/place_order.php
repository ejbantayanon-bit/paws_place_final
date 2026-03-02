<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$conn->set_charset('utf8mb4');

// POST to place an order with items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['items']) || !is_array($input['items']) || empty($input['items'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid request: items array required']);
        $conn->close();
        exit;
    }
    
    $order_source = isset($input['order_source']) ? $input['order_source'] : 'Manual_POS';
    $customer_name = isset($input['customer_name']) ? trim($input['customer_name']) : null;

    // Fallback: If no customer_name in request, try to get from session (set during Kiosk login)
    if (empty($customer_name) && isset($_SESSION['full_name'])) {
        $customer_name = trim($_SESSION['full_name']);
    }
    
    // Get cashier_id from session - only if user is a staff member (not KIOSK)
    // KIOSK users have student IDs which don't exist in the users table
    $cashier_id = null;
    $student_id = null;
    $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;
    if ($userRole !== 'KIOSK' && isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0) {
        $cashier_id = (int)$_SESSION['user_id'];
        
        // Verify cashier_id exists in local users table to prevent FK constraint error
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
        $check_stmt->bind_param('i', $cashier_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows === 0) {
            $cashier_id = null;
        }
        $check_stmt->close();
    } else if ($userRole === 'KIOSK' && isset($_SESSION['user_id'])) {
        $student_id = (string)$_SESSION['user_id'];
    }
    
    // Calculate total
    $total_amount = 0;
    foreach ($input['items'] as $item) {
        if (!isset($item['menu_item_id']) || !isset($item['quantity']) || !isset($item['price_at_sale'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid item structure']);
            $conn->close();
            exit;
        }
        $total_amount += $item['quantity'] * $item['price_at_sale'];
    }
    
    // Generate pre-order code: 3 digits + 1 letter (e.g., 100A)
    $pre_order_code = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . chr(65 + rand(0, 25));
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert order (cashier_id and student_id can be null depending on user type)
        if ($cashier_id === null && $student_id === null) {
            $stmt = $conn->prepare("INSERT INTO orders (pre_order_code, order_source, total_amount, status, cashier_id, student_id, customer_name, time_placed) VALUES (?, ?, ?, 'PENDING PAYMENT', NULL, NULL, ?, NOW())");
            $stmt->bind_param('ssds', $pre_order_code, $order_source, $total_amount, $customer_name);
        } else if ($cashier_id !== null) {
            $stmt = $conn->prepare("INSERT INTO orders (pre_order_code, order_source, total_amount, status, cashier_id, student_id, customer_name, time_placed) VALUES (?, ?, ?, 'PENDING PAYMENT', ?, NULL, ?, NOW())");
            $stmt->bind_param('ssdis', $pre_order_code, $order_source, $total_amount, $cashier_id, $customer_name);
        } else {
            $stmt = $conn->prepare("INSERT INTO orders (pre_order_code, order_source, total_amount, status, cashier_id, student_id, customer_name, time_placed) VALUES (?, ?, ?, 'PENDING PAYMENT', NULL, ?, ?, NOW())");
            $stmt->bind_param('ssdss', $pre_order_code, $order_source, $total_amount, $student_id, $customer_name);
        }
        if (!$stmt->execute()) {
            throw new Exception('Insert order failed: ' . $stmt->error);
        }
        $order_id = $conn->insert_id;
        $stmt->close();
        
        // If this is a GUEST order, track it in the session
        if ($student_id && strpos($student_id, 'GUEST') === 0) {
            if (!isset($_SESSION['guest_order_ids'])) {
                $_SESSION['guest_order_ids'] = [];
            }
            $_SESSION['guest_order_ids'][] = $order_id;
        }
        
        // Insert order items and consume from recipes
        foreach ($input['items'] as $item) {
            $menu_item_id = $item['menu_item_id'];
            $quantity = $item['quantity'];
            $price_at_sale = $item['price_at_sale'];
            $modifiers_json = isset($item['modifiers']) ? json_encode($item['modifiers']) : null;
            $external_item_name = isset($item['external_item_name']) ? $item['external_item_name'] : null;
            
            // Determine if we should save a local menu_item_id
            // If it's a cafeteria store, we might want to set menu_item_id to NULL if it's external
            $is_external = ($order_source !== 'Paws Place' && $order_source !== 'Manual_POS' && $order_source !== 'Kiosk');
            $db_menu_item_id = $is_external ? null : $menu_item_id;

            // Insert order item
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, external_item_name, quantity, price_at_sale, modifiers) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iisids', $order_id, $db_menu_item_id, $external_item_name, $quantity, $price_at_sale, $modifiers_json);
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
            $stmt->close();
            
            // Skip recipe/inventory for external (cafeteria) items
            if ($is_external) {
                continue;
            }

            // Get recipe for this menu item and consume inventory
            $recipe_stmt = $conn->prepare("SELECT raw_id, quantity_consumed FROM recipes WHERE menu_item_id = ?");
            $recipe_stmt->bind_param('i', $menu_item_id);
            $recipe_stmt->execute();
            $recipe_res = $recipe_stmt->get_result();
            
            while ($recipe_row = $recipe_res->fetch_assoc()) {
                $raw_id = $recipe_row['raw_id'];
                $consumed = $recipe_row['quantity_consumed'] * $quantity;
                
                // Update inventory_raw
                $inv_stmt = $conn->prepare("UPDATE inventory_raw SET quantity_on_hand = quantity_on_hand - ? WHERE raw_id = ?");
                $inv_stmt->bind_param('di', $consumed, $raw_id);
                if (!$inv_stmt->execute()) {
                    throw new Exception($inv_stmt->error);
                }
                $inv_stmt->close();
                
                // Log inventory change (cashier_id can be null for kiosk orders)
                if ($cashier_id !== null) {
                    $log_stmt = $conn->prepare("INSERT INTO inventory_logs (raw_id, user_id, change_amount, reason, log_date) VALUES (?, ?, ?, 'Order sale', NOW())");
                    $change_amount = -$consumed;
                    $log_stmt->bind_param('iid', $raw_id, $cashier_id, $change_amount);
                } else {
                    $log_stmt = $conn->prepare("INSERT INTO inventory_logs (raw_id, change_amount, reason, log_date) VALUES (?, ?, 'Order sale', NOW())");
                    $change_amount = -$consumed;
                    $log_stmt->bind_param('id', $raw_id, $change_amount);
                }
                if (!$log_stmt->execute()) {
                    throw new Exception($log_stmt->error);
                }
                $log_stmt->close();
            }
            $recipe_stmt->close();
        }
        
        $conn->commit();
        
        echo json_encode(['success' => true, 'order_id' => $order_id, 'pre_order_code' => $pre_order_code, 'total_amount' => $total_amount]);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Order creation failed: ' . $e->getMessage()]);
    }
    
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
