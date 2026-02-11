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

// GET /api/get_categories.php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = $conn->query("SELECT category_id, name, is_active, sort_order FROM categories WHERE is_active = 1 ORDER BY sort_order ASC");
    if (!$res) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $conn->error]);
        $conn->close();
        exit;
    }
    
    $categories = [];
    while ($row = $res->fetch_assoc()) {
        $categories[] = $row;
    }
    $res->free();
    
    echo json_encode(['success' => true, 'categories' => $categories]);
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
