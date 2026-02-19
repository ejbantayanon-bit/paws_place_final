<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../auth_check.php';

if ($current_user_role !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['user_id']) || empty($input['full_name']) || empty($input['role'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID, Name, and Role are required']);
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

// Construct UPDATE query
$sql = "UPDATE users SET full_name = ?, role = ?";
$types = "ss";
$params = [$input['full_name'], $input['role']];

if (!empty($input['password'])) {
    $sql .= ", password_hash = ?";
    $types .= "s";
    $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
}

$sql .= " WHERE user_id = ?";
$types .= "i";
$params[] = $input['user_id'];

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    // Log activity
    $log = $conn->prepare("INSERT INTO activity_logs (user_id, user_role, activity_type, description, created_at) VALUES (?, ?, 'USER_UPDATE', ?, NOW())");
    $desc = "Updated user ID " . $input['user_id'];
    $log->bind_param('iss', $current_user_id, $current_user_role, $desc);
    $log->execute();
    $log->close();

    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update user: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
