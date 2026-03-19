<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/GrubhoundAPI.php';
$api = new GrubhoundAPI();
echo "=== Items for [Supply] at Kennel Main (ID 1) ===\n";
print_r($api->getCafeteriaItemsByCategoryLocation(1, 'Supply'));
?>
