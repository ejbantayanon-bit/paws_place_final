<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

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

// POST to update inventory stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['raw_id']) || !isset($input['change_amount']) || !isset($input['reason'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'raw_id, change_amount, and reason required']);
        $conn->close();
        exit;
    }
    
    $raw_id = intval($input['raw_id']);
    $change_amount = floatval($input['change_amount']);
    $reason = $input['reason'];
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update inventory
        $stmt = $conn->prepare("UPDATE inventory_raw SET quantity_on_hand = quantity_on_hand + ? WHERE raw_id = ?");
        $stmt->bind_param('di', $change_amount, $raw_id);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();
        
        // Log the change
        $log_stmt = $conn->prepare("INSERT INTO inventory_logs (raw_id, user_id, change_amount, reason, log_date) VALUES (?, ?, ?, ?, NOW())");
        $log_stmt->bind_param('iids', $raw_id, $user_id, $change_amount, $reason);
        if (!$log_stmt->execute()) {
            throw new Exception($log_stmt->error);
        }
        $log_stmt->close();
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Inventory updated']);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
    }
    
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
