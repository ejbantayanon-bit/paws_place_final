<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../GrubhoundAPI.php';

// Parse JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$search = $input['search'] ?? $_GET['search'] ?? null;

if (!$search) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'search term required']);
    exit;
}

try {
    $api = new GrubhoundAPI();
    $results = $api->searchStudents($search);
    
    echo json_encode([
        'success' => true,
        'results' => $results
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
