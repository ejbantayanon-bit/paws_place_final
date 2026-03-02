<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/GrubhoundAPI.php';

$api = new GrubhoundAPI();
$locations = [
    1 => 'Kennel Main',
    2 => 'Kennel North',
    13 => 'Pup Stop'
];

foreach ($locations as $id => $name) {
    echo "=== $name (ID: $id) ===\n";
    
    // Get all categories first
    $allCatsResult = $api->getCafeteriaCategories();
    $rawCategories = [];
    if (is_array($allCatsResult) && isset($allCatsResult['items'])) {
        $rawCategories = $allCatsResult['items'];
    } elseif (is_array($allCatsResult) && isset($allCatsResult['data'])) {
        $rawCategories = $allCatsResult['data'];
    } elseif (is_array($allCatsResult)) {
        $rawCategories = $allCatsResult;
    }

    echo "Found " . count($rawCategories) . " total raw categories.\n";

    foreach ($rawCategories as $cat) {
        $catName = is_string($cat) ? $cat : ($cat['name'] ?? $cat['category'] ?? '');
        $catName = trim($catName);
        if (!$catName) continue;

        // Check items for this category at this location
        $itemsResult = $api->getCafeteriaItemsByCategoryLocation($id, $catName);
        $itemCount = 0;
        
        if (is_array($itemsResult)) {
            if (isset($itemsResult['items'])) {
                $itemCount = count($itemsResult['items']);
            } elseif (isset($itemsResult['data'])) {
                $itemCount = count($itemsResult['data']);
            } elseif (!isset($itemsResult['success'])) {
                $itemCount = count($itemsResult);
            }
        }

        if ($itemCount > 0) {
            echo "Category: [$catName] has $itemCount items.\n";
        }
    }
    echo "\n";
}
?>
