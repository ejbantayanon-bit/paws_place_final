<?php
header('Content-Type: text/plain');

$locations = [
    1 => 'Kennel Main',
    2 => 'Kennel North',
    13 => 'Pup Stop'
];

foreach ($locations as $id => $name) {
    echo "=== Filtered Categories for $name (ID: $id) ===\n";
    $url = "http://localhost/paws_place_final/paw_place/server/api/get_cafeteria_categories.php?location_id=$id";
    $result = @file_get_contents($url);
    $data = json_decode($result, true);
    
    if ($data && $data['success']) {
        foreach ($data['categories'] as $cat) {
            echo "- " . $cat['name'] . "\n";
        }
    } else {
        echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}
?>
