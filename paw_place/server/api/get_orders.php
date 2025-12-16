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

// GET orders: /api/get_orders.php?status=PENDING_PAYMENT or all
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $status = isset($_GET['status']) ? strtoupper($_GET['status']) : null;
    
    if ($status) {
        $stmt = $conn->prepare("SELECT order_id, pre_order_code, final_code, order_source, total_amount, status, time_placed, time_paid FROM orders WHERE status = ? ORDER BY time_placed DESC");
        $stmt->bind_param('s', $status);
    } else {
        $stmt = $conn->prepare("SELECT order_id, pre_order_code, final_code, order_source, total_amount, status, time_placed, time_paid FROM orders ORDER BY time_placed DESC LIMIT 50");
    }
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $conn->error]);
        $conn->close();
        exit;
    }
    
    $stmt->execute();
    $res = $stmt->get_result();
    $orders = [];
    while ($row = $res->fetch_assoc()) {
        // Get order items for each order
        $oi_stmt = $conn->prepare("SELECT order_item_id, menu_item_id, quantity, price_at_sale, modifiers FROM order_items WHERE order_id = ?");
        $oi_stmt->bind_param('i', $row['order_id']);
        $oi_stmt->execute();
        $oi_res = $oi_stmt->get_result();
        $items = [];
        while ($oi_row = $oi_res->fetch_assoc()) {
            $items[] = $oi_row;
        }
        $oi_stmt->close();
        $row['order_items'] = $items;
        $orders[] = $row;
    }
    $stmt->close();
    
    echo json_encode(['success' => true, 'orders' => $orders]);
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
