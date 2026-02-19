<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../auth_check.php';

// Only Admin can list users
if ($current_user_role !== 'Admin') {
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

$res = $conn->query("SELECT user_id, username, full_name, role, created_at FROM users ORDER BY full_name ASC");

if (!$res) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $conn->error]);
    $conn->close();
    exit;
}

$users = [];
while ($row = $res->fetch_assoc()) {
    $users[] = $row;
}
$res->free();

echo json_encode(['success' => true, 'users' => $users]);

$conn->close();
?>
