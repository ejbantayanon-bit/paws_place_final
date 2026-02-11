<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../auth_check.php';

// Only Admin can view user list
if ($current_user_role !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// DB config
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    $sql = "SELECT user_id, username, full_name, role FROM users ORDER BY full_name ASC";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['success' => true, 'users' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
