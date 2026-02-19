<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../auth_check.php';

// Allow all authenticated staff to read settings, but maybe frontend restricts view.
// For now, let's keep it restricted to Admin for "Management" purposes, 
// though other parts of app might need to read 'maintenance_mode'.
// Let's assume this API is for the Admin Panel management view.

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

$res = $conn->query("SELECT setting_key, setting_value, updated_at FROM settings");

if (!$res) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $conn->error]);
    $conn->close();
    exit;
}

$settings = [];
while ($row = $res->fetch_assoc()) {
    $settings[$row['setting_key']] = $row;
}
$res->free();

echo json_encode(['success' => true, 'settings' => $settings]);

$conn->close();
?>
