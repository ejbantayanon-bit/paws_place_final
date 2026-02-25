<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

// Categories that are NOT food/drink related — exclude from the ordering page
$excludedCategories = [
    'Optimum Cash Incentives',
    'Catering',
    'Consignment',
    'Room Rental',
    'School',
    'School Supply',
    'School Uniform',
    'Spray',
    'Supply',
    'Vending Machine',
];

try {
    $api = new GrubhoundAPI();
    $result = $api->getCafeteriaCategories();
    
    // API returns {status: 200, items: [...]}
    $categories = $result;
    if (is_array($result) && isset($result['items'])) {
        $categories = $result['items'];
    } elseif (is_array($result) && isset($result['data'])) {
        $categories = $result['data'];
    }

    // Filter out non-food categories
    if (is_array($categories)) {
        $categories = array_values(array_filter($categories, function($cat) use ($excludedCategories) {
            $name = is_string($cat) ? trim($cat) : trim($cat['category'] ?? $cat['name'] ?? '');
            return !in_array($name, $excludedCategories);
        }));
    }
    
    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
