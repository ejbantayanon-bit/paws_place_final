<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
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

// Default limit
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
if ($limit > 500) $limit = 500;

// Fetch logs with user details if available (left join in case user was deleted, though logs should store snapshot)
// Actually logs store user_id and user_role. We can join users table to get current username if needed, 
// but logs might have historical data. Let's join users for display name, fallback to ID.
$query = "
    SELECT l.log_id, l.user_id, l.user_role, l.activity_type, l.description, l.created_at, u.full_name, u.username 
    FROM activity_logs l 
    LEFT JOIN users u ON l.user_id = u.user_id 
    ORDER BY l.created_at DESC 
    LIMIT ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $limit);

if (!$stmt) {
    // try fallback without join if users table has issues
    $res = $conn->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT $limit");
} else {
    $stmt->execute();
    $res = $stmt->get_result();
}

$logs = [];
while ($row = $res->fetch_assoc()) {
    $row['user_display'] = $row['full_name'] ? $row['full_name'] : ($row['username'] ? $row['username'] : 'User #' . $row['user_id']);
    $logs[] = $row;
}

echo json_encode(['success' => true, 'logs' => $logs]);

$conn->close();
?>
