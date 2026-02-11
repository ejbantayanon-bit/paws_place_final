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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = $conn->query("SELECT modifier_id, name, display_type, price_add, applicable_category_id FROM modifiers ORDER BY name ASC");
    if (!$res) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $conn->error]);
        $conn->close();
        exit;
    }
    $mods = [];
    while ($row = $res->fetch_assoc()) {
        $mods[] = $row;
    }
    $res->free();
    echo json_encode(['success' => true, 'modifiers' => $mods]);
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>