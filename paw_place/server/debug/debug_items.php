<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/GrubhoundAPI.php';

$api = new GrubhoundAPI();
$locationId = 13; // Pup Stop
$categories = ['Drinks', 'Consignment', 'Food', 'Snacks'];

foreach ($categories as $cat) {
    echo "=== Items in [$cat] for Pup Stop ===\n";
    $result = $api->getCafeteriaItemsByCategoryLocation($locationId, $cat);
    
    $items = [];
    if (is_array($result) && isset($result['items'])) {
        $items = $result['items'];
    } elseif (is_array($result) && isset($result['data'])) {
        $items = $result['data'];
    } elseif (is_array($result)) {
        $items = $result;
    }

    foreach ($items as $it) {
        $name = $it['name'] ?? $it['item_name'] ?? 'Unknown';
        echo "- $name\n";
    }
    echo "\n";
}
?>
