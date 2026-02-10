<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$DB_HOST = '172.16.50.93';
$DB_USER = 'paws_user';
$DB_PASS = 'Cy123';
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
    
    $view = isset($_GET['view']) ? $_GET['view'] : null;
    
    if ($status) {
        $stmt = $conn->prepare("SELECT order_id, pre_order_code, final_code, customer_name, order_source, total_amount, status, time_placed, time_paid FROM orders WHERE status = ? ORDER BY time_placed DESC");
        $stmt->bind_param('s', $status);
    } elseif ($view === 'history') {
        // Fetch All Paid orders (Preparing, Ready, Served) and Cancelled orders
        $stmt = $conn->prepare("SELECT order_id, pre_order_code, final_code, customer_name, order_source, total_amount, status, time_placed, time_paid FROM orders WHERE status IN ('PREPARING', 'READY', 'SERVED', 'CANCELLED') ORDER BY time_placed DESC LIMIT 200");
    } else {
        // Default: Fetch latest 50 orders of any status (useful for tracker/overview but might miss old completed ones)
        // Ideally the frontend uses specific status filters for specific views.
        $stmt = $conn->prepare("SELECT order_id, pre_order_code, final_code, customer_name, order_source, total_amount, status, time_placed, time_paid FROM orders ORDER BY time_placed DESC LIMIT 50");
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
        // Get order items for each order with menu item names
        $oi_stmt = $conn->prepare("SELECT oi.order_item_id, oi.menu_item_id, mi.name, oi.quantity, oi.price_at_sale, oi.modifiers FROM order_items oi LEFT JOIN menu_items mi ON oi.menu_item_id = mi.item_id WHERE oi.order_id = ?");
        $oi_stmt->bind_param('i', $row['order_id']);
        $oi_stmt->execute();
        $oi_res = $oi_stmt->get_result();
        $items = [];
        while ($oi_row = $oi_res->fetch_assoc()) {
            $items[] = $oi_row;
        }
        $oi_stmt->close();
        $row['order_items'] = $items;
        
        // Get payment info (cash paid amount)
        $payment_stmt = $conn->prepare("SELECT amount FROM payments WHERE order_id = ? ORDER BY payment_id DESC LIMIT 1");
        $payment_stmt->bind_param('i', $row['order_id']);
        $payment_stmt->execute();
        $payment_res = $payment_stmt->get_result();
        $payment_row = $payment_res->fetch_assoc();
        $row['cash_paid'] = $payment_row ? floatval($payment_row['amount']) : floatval($row['total_amount']);
        $payment_stmt->close();
        
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
