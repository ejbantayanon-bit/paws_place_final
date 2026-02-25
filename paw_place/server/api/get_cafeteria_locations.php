<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

try {
    $api = new GrubhoundAPI();
    $result = $api->getCafeteriaLocations();
    
    // API returns {status: 200, items: [...]} or similar
    $locations = $result;
    if (is_array($result) && isset($result['items'])) {
        $locations = $result['items'];
    } elseif (is_array($result) && isset($result['data'])) {
        $locations = $result['data'];
    }
    
    echo json_encode(['success' => true, 'locations' => $locations]);
} catch (Exception $e) {
    // Location API may not be active yet — return empty
    echo json_encode(['success' => true, 'locations' => [], 'note' => $e->getMessage()]);
}
?>
