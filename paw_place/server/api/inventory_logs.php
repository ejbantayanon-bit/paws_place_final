<?php
header('Content-Type: application/json; charset=utf-8');

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

// GET inventory logs with optional filtering
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $raw_id = isset($_GET['raw_id']) ? intval($_GET['raw_id']) : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
    
    if ($raw_id) {
        $stmt = $conn->prepare("SELECT log_id, raw_id, user_id, change_amount, reason, log_date FROM inventory_logs WHERE raw_id = ? ORDER BY log_date DESC LIMIT ?");
        $stmt->bind_param('ii', $raw_id, $limit);
    } else {
        $stmt = $conn->prepare("SELECT log_id, raw_id, user_id, change_amount, reason, log_date FROM inventory_logs ORDER BY log_date DESC LIMIT ?");
        $stmt->bind_param('i', $limit);
    }
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $conn->error]);
        $conn->close();
        exit;
    }
    
    $stmt->execute();
    $res = $stmt->get_result();
    $logs = [];
    while ($row = $res->fetch_assoc()) {
        $logs[] = $row;
    }
    $stmt->close();
    
    echo json_encode(['success' => true, 'logs' => $logs]);
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
