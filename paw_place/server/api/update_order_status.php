<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$DB_HOST = '172.16.50.93';
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

// POST to update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['order_id']) || !isset($input['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'order_id and status required']);
        $conn->close();
        exit;
    }
    
    $order_id = intval($input['order_id']);
    $status = strtoupper($input['status']);
    $cash_paid = isset($input['cash_paid']) ? floatval($input['cash_paid']) : null;
    $valid_statuses = ['PENDING PAYMENT', 'PREPARING', 'READY', 'SERVED', 'CANCELLED', 'COMPLETED'];
    
    if (!in_array($status, $valid_statuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        $conn->close();
        exit;
    }
    
    // Update order status
    $sql = "UPDATE orders SET status = ?";
    $params = [$status];
    $types = "s";

    // If status is PREPARING (Payment Received), update time_paid
    if ($status === 'PREPARING') {
        $sql .= ", time_paid = NOW()";
    }
    
    // If status is SERVED, potentially update a completed timestamp if it existed, 
    // but for now we rely on status. We can also ensure time_paid is set if it wasn't.
    if ($status === 'SERVED') {
        // Optional: Ensure time_paid is set if it's null (fallback)
        $sql .= ", time_paid = COALESCE(time_paid, NOW())";
    }

    $sql .= " WHERE order_id = ?";
    $params[] = $order_id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    
    // If cash_paid is provided and status is PREPARING, store it in payments table
    if ($cash_paid !== null && $status === 'PREPARING') {
        $payment_method = 'Cash';
        $payment_stmt = $conn->prepare("INSERT INTO payments (order_id, payment_method, amount) VALUES (?, ?, ?)");
        $payment_stmt->bind_param('isd', $order_id, $payment_method, $cash_paid);
        $payment_stmt->execute();
        $payment_stmt->close();
    }
    
    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Order status updated']);
    }
    
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
