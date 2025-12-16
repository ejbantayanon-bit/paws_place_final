<?php
header('Content-Type: application/json; charset=utf-8');

// Simple validator that checks if a provided password matches any Admin/Cashier user
// Does NOT create a session; used by kiosk exit to verify staff password client-side.

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$password = isset($input['password']) ? $input['password'] : '';

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password required']);
    $conn->close();
    exit;
}

function verify_pass($input, $stored) {
    if (password_get_info($stored)['algo'] !== 0) {
        return password_verify($input, $stored);
    }
    return hash_equals($stored, $input);
}

$stmt = $conn->prepare("SELECT user_id, username, password_hash, full_name, role FROM users WHERE role IN ('Admin','Cashier')");
$stmt->execute();
$res = $stmt->get_result();
$found = false;
$userRow = null;
while ($row = $res->fetch_assoc()) {
    if (verify_pass($password, $row['password_hash'])) {
        $found = true;
        $userRow = $row;
        break;
    }
}

if ($found) {
    echo json_encode(['success' => true, 'user_id' => $userRow['user_id'], 'username' => $userRow['username'], 'role' => $userRow['role'], 'full_name' => $userRow['full_name']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
}

$stmt->close();
$conn->close();
exit;

?>
