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
    $cashier_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
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
    
    // Generate pre-order code
    $pre_order_code = 'PRE-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (pre_order_code, order_source, total_amount, status, cashier_id, time_placed) VALUES (?, ?, ?, 'PENDING PAYMENT', ?, NOW())");
        $stmt->bind_param('ssdi', $pre_order_code, $order_source, $total_amount, $cashier_id);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $order_id = $conn->insert_id;
        $stmt->close();
        
        // Insert order items and consume from recipes
        foreach ($input['items'] as $item) {
            $menu_item_id = $item['menu_item_id'];
            $quantity = $item['quantity'];
            $price_at_sale = $item['price_at_sale'];
            $modifiers_json = isset($item['modifiers']) ? json_encode($item['modifiers']) : null;
            
            // Insert order item
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price_at_sale, modifiers) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('iiiis', $order_id, $menu_item_id, $quantity, $price_at_sale, $modifiers_json);
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
            $stmt->close();
            
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
                
                // Log inventory change
                $log_stmt = $conn->prepare("INSERT INTO inventory_logs (raw_id, user_id, change_amount, reason, log_date) VALUES (?, ?, ?, 'Order sale', NOW())");
                $change_amount = -$consumed;
                $log_stmt->bind_param('iid', $raw_id, $cashier_id, $change_amount);
                $log_stmt->execute();
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
