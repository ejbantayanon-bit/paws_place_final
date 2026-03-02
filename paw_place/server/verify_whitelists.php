<?php
header('Content-Type: text/plain');

$locations = [
    1 => 'Kennel Main',
    13 => 'Pup Stop',
    2 => 'Kennel North'
];

foreach ($locations as $id => $name) {
    echo "=== Final Categories for $name (ID: $id) ===\n";
    $url = "http://localhost/paws_place_final/paw_place/server/api/get_cafeteria_categories.php?location_id=$id";
    $result = @file_get_contents($url);
    $data = json_decode($result, true);
    
    if ($data && isset($data['success']) && $data['success']) {
        foreach ($data['categories'] as $cat) {
            echo "- " . $cat['name'] . "\n";
        }
    } else {
        echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}
?>
