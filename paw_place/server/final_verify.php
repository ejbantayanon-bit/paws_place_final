<?php
header('Content-Type: text/plain');

$checks = [
    ['id' => 1, 'cat' => 'Milk Tea', 'store' => 'Kennel Main'],
    ['id' => 1, 'cat' => 'Coffee', 'store' => 'Kennel Main'],
    ['id' => 13, 'cat' => 'Consignment', 'store' => 'Pup Stop'],
    ['id' => 13, 'cat' => 'Drinks', 'store' => 'Pup Stop']
];

foreach ($checks as $check) {
    echo "=== Items for [{$check['cat']}] at {$check['store']} ===\n";
    $url = "http://localhost/paws_place_final/paw_place/server/api/get_cafeteria_items.php?location_id={$check['id']}&category=" . urlencode($check['cat']);
    $result = @file_get_contents($url);
    $data = json_decode($result, true);
    
    if ($data && $data['success']) {
        foreach ($data['items'] as $it) {
            echo "- " . ($it['name'] ?? $it['item_name']) . " (₱" . number_format($it['price'], 2) . ")\n";
        }
    } else {
        echo "Error or no items: " . ($data['message'] ?? 'Unknown') . "\n";
    }
    echo "\n";
}
?>
