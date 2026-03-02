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

// GET student's order history
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get student_id from session or query parameter
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
    
    if (!$student_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'student_id required']);
        $conn->close();
        exit;
    }
    
    // Convert to string for comparison
    $student_id = (string)$student_id;
    
    try {
        // If student is EXACTLY 'GUEST', only show orders from current session
        if ($student_id === 'GUEST') {
            if (!isset($_SESSION['guest_order_ids']) || empty($_SESSION['guest_order_ids'])) {
                echo json_encode(['success' => true, 'student_id' => 'GUEST', 'orders' => [], 'count' => 0]);
                exit;
            }
            
            $guest_ids = $_SESSION['guest_order_ids'];
            // Sanitize IDs
            $ids_placeholder = implode(',', array_fill(0, count($guest_ids), '?'));
            $types = str_repeat('i', count($guest_ids));
            
            $stmt = $conn->prepare("
                SELECT o.order_id, o.pre_order_code, o.final_code, o.customer_name, o.order_source, 
                       o.total_amount, o.status, o.time_placed, o.time_paid, u.full_name AS cashier_name
                FROM orders o
                LEFT JOIN users u ON o.cashier_id = u.user_id
                WHERE o.order_id IN ($ids_placeholder) 
                ORDER BY o.time_placed DESC
            ");
            $stmt->bind_param($types, ...$guest_ids);
        } else {
            // Normal student/employee lookup
            $stmt = $conn->prepare("
                SELECT o.order_id, o.pre_order_code, o.final_code, o.customer_name, o.order_source, 
                       o.total_amount, o.status, o.time_placed, o.time_paid, u.full_name AS cashier_name
                FROM orders o
                LEFT JOIN users u ON o.cashier_id = u.user_id
                WHERE o.student_id = ? 
                ORDER BY o.time_placed DESC 
                LIMIT 50
            ");
            $stmt->bind_param('s', $student_id);
        }
        
        $stmt->execute();
        $ordersRes = $stmt->get_result();
        
        $orders = [];
        while ($order = $ordersRes->fetch_assoc()) {
            // Get items for this order
            $itemStmt = $conn->prepare("
                SELECT oi.order_item_id, oi.menu_item_id, COALESCE(mi.name, oi.external_item_name) AS name, oi.quantity, oi.price_at_sale, oi.modifiers
                FROM order_items oi
                LEFT JOIN menu_items mi ON oi.menu_item_id = mi.item_id
                WHERE oi.order_id = ?
            ");
            $itemStmt->bind_param('i', $order['order_id']);
            $itemStmt->execute();
            $itemsRes = $itemStmt->get_result();
            
            $items = [];
            while ($item = $itemsRes->fetch_assoc()) {
                $item['modifiers'] = $item['modifiers'] ? json_decode($item['modifiers'], true) : [];
                $items[] = $item;
            }
            $itemStmt->close();
            
            $order['items'] = $items;
            $orders[] = $order;
        }
        $stmt->close();
        
        echo json_encode([
            'success' => true,
            'student_id' => $student_id,
            'orders' => $orders,
            'count' => count($orders)
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
