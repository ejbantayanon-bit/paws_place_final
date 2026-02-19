<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../auth_check.php';

if ($current_user_role !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['username']) || empty($input['password']) || empty($input['full_name']) || empty($input['role'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
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

// Check if username exists
$check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$check->bind_param('s', $input['username']);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already exists']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Insert user
$stmt = $conn->prepare("INSERT INTO users (username, password_hash, full_name, role, created_at) VALUES (?, ?, ?, ?, NOW())");
$hash = password_hash($input['password'], PASSWORD_DEFAULT);
$stmt->bind_param('ssss', $input['username'], $hash, $input['full_name'], $input['role']);

if ($stmt->execute()) {
    // Log activity
    $log = $conn->prepare("INSERT INTO activity_logs (user_id, user_role, activity_type, description, created_at) VALUES (?, ?, 'USER_CREATE', ?, NOW())");
    $desc = "Created user " . $input['username'] . " (" . $input['role'] . ")";
    $log->bind_param('iss', $current_user_id, $current_user_role, $desc);
    $log->execute();
    $log->close();

    echo json_encode(['success' => true, 'message' => 'User created successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create user: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
