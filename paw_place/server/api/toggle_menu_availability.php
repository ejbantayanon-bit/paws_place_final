<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// Only Admin and Cashier can toggle availability
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Cashier'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$DB_HOST = '172.16.50.93';
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['item_id']) || !isset($input['is_available'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'item_id and is_available required']);
        $conn->close();
        exit;
    }
    
    $item_id = intval($input['item_id']);
    $is_available = $input['is_available'] ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE menu_items SET is_available = ? WHERE item_id = ?");
    $stmt->bind_param('ii', $is_available, $item_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Availability updated']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
