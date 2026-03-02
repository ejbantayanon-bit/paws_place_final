<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    die('Unauthorized');
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    http_response_code(500);
    die('Database connection failed');
}
$conn->set_charset('utf8mb4');

// Headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=sales_report_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

// CSV Header
// Headers are set below query logic to ensure columns match data


// Get filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Base query
$sql = "SELECT order_id, time_placed, status, customer_name, total_amount, pre_order_code, order_source 
        FROM orders WHERE 1=1";
$params = [];
$types = "";

if (!empty($start_date)) {
    $sql .= " AND DATE(time_placed) >= ?";
    $params[] = $start_date;
    $types .= "s";
}

if (!empty($end_date)) {
    $sql .= " AND DATE(time_placed) <= ?";
    $params[] = $end_date;
    $types .= "s";
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

if (!empty($search)) {
    $searchTerm = "%$search%";
    $sql .= " AND (customer_name LIKE ? OR pre_order_code LIKE ?)";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

$sql .= " ORDER BY time_placed DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Update Header
fputcsv($output, ['Order ID', 'Date/Time', 'Type', 'Customer Name', 'Items List', 'Total Items', 'Total Amount', 'Status']);

while ($row = $result->fetch_assoc()) {
    // Get detailed items
    $itemSql = "SELECT COALESCE(mi.name, oi.external_item_name) AS name, oi.quantity 
                FROM order_items oi 
                LEFT JOIN menu_items mi ON oi.menu_item_id = mi.item_id 
                WHERE oi.order_id = ?";
    
    $itemStmt = $conn->prepare($itemSql);
    $itemStmt->bind_param('i', $row['order_id']);
    $itemStmt->execute();
    $itemRes = $itemStmt->get_result();
    
    $itemsList = [];
    $totalQty = 0;
    
    while ($item = $itemRes->fetch_assoc()) {
        $itemsList[] = $item['name'] . " (" . $item['quantity'] . ")";
        $totalQty += $item['quantity'];
    }
    $itemStmt->close();

    $itemsString = implode(", ", $itemsList);

    fputcsv($output, [
        $row['pre_order_code'],
        $row['time_placed'],
        $row['order_source'],
        $row['customer_name'],
        $itemsString,
        $totalQty,
        number_format($row['total_amount'], 2),
        $row['status']
    ]);
}

fclose($output);
$conn->close();
exit;
?>
