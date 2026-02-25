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
    'Other',
    'Miscellaneous',
    'Kit',
    'Inventory Kit',
    'Kit Items',
    'Kit Components',
];

$locationId = $_GET['location_id'] ?? null;

try {
    $api = new GrubhoundAPI();
    $result = $api->getCafeteriaCategories();
    
    $rawCategories = [];
    if (is_array($result) && isset($result['items'])) {
        $rawCategories = $result['items'];
    } elseif (is_array($result) && isset($result['data'])) {
        $rawCategories = $result['data'];
    } elseif (is_array($result) && !isset($result['success'])) {
        $rawCategories = $result;
    }

    $categories = [];
    foreach ($rawCategories as $cat) {
        $name = is_string($cat) ? $cat : ($cat['name'] ?? $cat['category'] ?? '');
        $name = trim($name);
        if (!$name) continue;

        // Skip excluded categories (case-insensitive)
        $lowerName = strtolower($name);
        $lowerExcluded = array_map('strtolower', $excludedCategories);
        if (in_array($lowerName, $lowerExcluded)) continue;

        // If location_id is provided, check if this category has items in that location
        if ($locationId) {
            try {
                $itemCheck = $api->getCafeteriaItemsByCategoryLocation($locationId, $name);
                $hasItems = false;
                if (is_array($itemCheck)) {
                    if (isset($itemCheck['items']) && count($itemCheck['items']) > 0) $hasItems = true;
                    elseif (isset($itemCheck['data']) && count($itemCheck['data']) > 0) $hasItems = true;
                    elseif (!isset($itemCheck['success']) && count($itemCheck) > 0) $hasItems = true;
                }
                
                if (!$hasItems) continue; // Skip category if it has no items for this location
            } catch (Exception $e) {
                // If check fails, we might still want to show the category or skip it
                // For safety, let's skip it to ensure we ONLY show categories with items
                continue;
            }
        }

        $categories[] = [
            'id' => $name,
            'category' => $name,
            'name' => $name
        ];
    }
    
    // Sort categories alphabetically
    usort($categories, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
