<?php
require_once __DIR__ . '/GrubhoundAPI.php';
$output = "";
try {
    $api = new GrubhoundAPI();
    
    // Pupstop location is ID 13 or 2
    $locId = 13; 
    $catName = "Milktea And Ice Coffee";
    
    $output .= "--- ITEMS FOR $catName AT LOCATION $locId ---\n";
    $items = $api->getCafeteriaItemsByCategoryLocation($locId, $catName);
    $output .= print_r($items, true) . "\n";

    $cat2 = "Coffee And Milktea 2-paws Place";
    $output .= "\n--- ITEMS FOR $cat2 AT LOCATION $locId ---\n";
    $items2 = $api->getCafeteriaItemsByCategoryLocation($locId, $cat2);
    $output .= print_r($items2, true) . "\n";

} catch (Exception $e) {
    $output .= "Error: " . $e->getMessage();
}
file_put_contents(__DIR__ . '/debug_items.txt', $output);
echo "Debug output saved to server/debug_items.txt\n";
?>
