<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../auth_check.php';

if ($current_user_role !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

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
$id = $input['user_id'] ?? null;
$fullName = $input['full_name'] ?? '';
$username = $input['username'] ?? '';
$role = $input['role'] ?? 'Cashier';
$password = $input['password'] ?? '';

if (empty($fullName) || empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    if ($id) {
        // Update existing
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, role=?, password_hash=? WHERE user_id=?");
            $stmt->bind_param("ssssi", $fullName, $username, $role, $hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, role=? WHERE user_id=?");
            $stmt->bind_param("sssi", $fullName, $username, $role, $id);
        }
    } else {
        // Create new
        if (empty($password)) {
            throw new Error("Password is required for new accounts");
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, role, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullName, $username, $role, $hash);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Staff saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving staff: ' . $stmt->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
