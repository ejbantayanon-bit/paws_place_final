<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

$category = $_GET['category'] ?? null;
$locationId = $_GET['location_id'] ?? null;

if (!$category) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'category parameter is required']);
    exit;
}

try {
    $api = new GrubhoundAPI();

    if ($locationId) {
        $result = $api->getCafeteriaItemsByCategoryLocation($locationId, $category);
    } else {
        $result = $api->getCafeteriaItemsByCategory($category);
    }

    // API returns {status: 200, items: [...]}
    $items = $result;
    if (is_array($result) && isset($result['items'])) {
        $items = $result['items'];
    } elseif (is_array($result) && isset($result['data'])) {
        $items = $result['data'];
    }

    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
