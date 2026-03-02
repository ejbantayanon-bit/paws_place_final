<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

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

// Get filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

// Base Query
$sql = "SELECT order_id, time_placed, status, customer_name, total_amount, pre_order_code, order_source 
        FROM orders WHERE 1=1";
$params = [];
$types = "";

if ($start_date) {
    $sql .= " AND DATE(time_placed) >= ?";
    $params[] = $start_date;
    $types .= "s";
}

if ($end_date) {
    $sql .= " AND DATE(time_placed) <= ?";
    $params[] = $end_date;
    $types .= "s";
}

$sql .= " ORDER BY time_placed DESC LIMIT ?";
$params[] = $limit;
$types .= "i";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    // Get item details
    $itemsStmt = $conn->prepare("SELECT oi.quantity, COALESCE(mi.name, oi.external_item_name) AS name FROM order_items oi LEFT JOIN menu_items mi ON oi.menu_item_id = mi.item_id WHERE oi.order_id = ?");
    $itemsStmt->bind_param('i', $row['order_id']);
    $itemsStmt->execute();
    $itemsRes = $itemsStmt->get_result();
    
    $items = [];
    while($item = $itemsRes->fetch_assoc()) {
        $items[] = $item;
    }
    $itemsStmt->close();
    
    $row['order_items'] = $items;
    $row['item_count'] = count($items);
    
    $orders[] = $row;
}

echo json_encode(['success' => true, 'orders' => $orders]);

$stmt->close();
$conn->close();
?>
