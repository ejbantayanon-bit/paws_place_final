<?php
header('Content-Type: application/json; charset=utf-8');

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

// GET inventory raw materials
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = $conn->query("SELECT raw_id, name, unit_of_measure, quantity_on_hand, reorder_point, cost_per_unit FROM inventory_raw ORDER BY name ASC");
    
    if (!$res) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $conn->error]);
        $conn->close();
        exit;
    }
    
    $items = [];
    while ($row = $res->fetch_assoc()) {
        $row['is_low_stock'] = floatval($row['quantity_on_hand']) <= floatval($row['reorder_point']);
        $items[] = $row;
    }
    $res->free();
    
    echo json_encode(['success' => true, 'inventory' => $items]);
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
