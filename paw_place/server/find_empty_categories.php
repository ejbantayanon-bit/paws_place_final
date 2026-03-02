<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/GrubhoundAPI.php';

$api = new GrubhoundAPI();
$locations = [1, 2, 13];

// Copy of mapping from get_cafeteria_categories.php
$friendlyCategoryMap = [
    'Coffee And Milktea 2-paws Place' => 'Coffee',
    'Milktea And Ice Coffee' => 'Milk Tea',
    'Drinks' => 'Drinks',
    'Fruits' => 'Fruits',
    'Ice Cream' => 'Ice Cream',
    'Ice Cream in Cups' => 'Ice Cream',
    'Snacks' => 'Snacks',
    'Bread' => 'Bread',
    'Candy' => 'Candy',
    'Food' => 'Food',
    'Consignment' => 'Consignment',
    'Supply' => 'Supply',
];

$res = $api->getCafeteriaCategories();
$rawCategories = $res['items'] ?? $res['data'] ?? $res ?? [];

foreach ($locations as $locId) {
    echo "=== Checking Empty Categories for Location ID: $locId ===\n";
    
    // Group raw categories by friendly name
    $grouped = [];
    foreach ($rawCategories as $cat) {
        $rawName = is_string($cat) ? $cat : ($cat['name'] ?? $cat['category'] ?? '');
        $rawName = trim($rawName);
        if (!$rawName) continue;
        $friendly = $friendlyCategoryMap[$rawName] ?? $rawName;
        $grouped[$friendly][] = $rawName;
    }

    foreach ($grouped as $friendly => $rawNames) {
        $totalItems = 0;
        foreach ($rawNames as $raw) {
            $itemCheck = $api->getCafeteriaItemsByCategoryLocation($locId, $raw);
            $items = $itemCheck['items'] ?? $itemCheck['data'] ?? (isset($itemCheck['success']) ? [] : $itemCheck);
            $totalItems += is_array($items) ? count($items) : 0;
        }
        
        if ($totalItems === 0) {
            echo "Category [$friendly] is EMPTY for location $locId\n";
        } else {
            // echo "Category [$friendly] has $totalItems items.\n";
        }
    }
    echo "\n";
}
?>
