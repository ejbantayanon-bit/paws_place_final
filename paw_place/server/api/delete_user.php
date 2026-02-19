<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../auth_check.php';

if ($current_user_role !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

if ($input['user_id'] == $current_user_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
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

$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param('i', $input['user_id']);

if ($stmt->execute()) {
    // Log activity
    $log = $conn->prepare("INSERT INTO activity_logs (user_id, user_role, activity_type, description, created_at) VALUES (?, ?, 'USER_DELETE', ?, NOW())");
    $desc = "Deleted user ID " . $input['user_id'];
    $log->bind_param('iss', $current_user_id, $current_user_role, $desc);
    $log->execute();
    $log->close();

    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete user: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
