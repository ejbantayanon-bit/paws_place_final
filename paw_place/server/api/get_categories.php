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

// GET /api/get_categories.php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Optional: Filter by active status
    $sql = "SELECT category_id, name, is_active, sort_order FROM categories ORDER BY sort_order ASC, name ASC";
    
    $result = $conn->query($sql);
    $categories = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'categories' => $categories]);
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
