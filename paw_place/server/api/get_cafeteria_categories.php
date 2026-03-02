<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

// Categories that are NOT food/drink related — exclude from the ordering page
$excludedCategories = [
    'Optimum Cash Incentives',
    'Catering',
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
    'Uniform',
    'School Uniform',
    'Medicine',
    'Personal Care',
    'Cleaning',
];

// Mapping of ugly MIS category names to user-friendly names
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

$locationIdParam = $_GET['location_id'] ?? '';
$locationId = ($locationIdParam !== '') ? (int)$locationIdParam : null;

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

// Strict Whitelists for specific locations as requested by the user
$locationWhitelists = [
    1 => ['Bread', 'Candy', 'Drinks', 'Food', 'Fruits', 'Ice Cream', 'Snacks', 'Supply'],
    2 => ['Bread', 'Candy', 'Drinks', 'Food', 'Fruits', 'Ice Cream', 'Snacks', 'Supply'], // Default for Kennel North
    13 => ['Bread', 'Candy', 'Consignment', 'Drinks', 'Food', 'Fruits', 'Snacks']
];

// 1. Group raw categories by friendly names and determine visibility
$groupedRawNames = [];
foreach ($rawCategories as $cat) {
    $rawName = is_string($cat) ? $cat : ($cat['name'] ?? $cat['category'] ?? '');
    $rawName = trim($rawName);
    if (!$rawName) continue;

    $friendlyName = $friendlyCategoryMap[$rawName] ?? $rawName;
    
    // Whitelist/Blacklist Filtering
    if ($locationId !== null && isset($locationWhitelists[$locationId])) {
        // Use friendly name for whitelist check
        if (!in_array($friendlyName, $locationWhitelists[$locationId])) continue;
    } else {
        // Global blacklist using raw name (lowercase comparison)
        $lowerRawName = strtolower($rawName);
        $lowerExcluded = array_map('strtolower', $excludedCategories);
        if (in_array($lowerRawName, $lowerExcluded)) continue;
        
        // Also check friendly name against blacklist
        $lowerFriendly = strtolower($friendlyName);
        if (in_array($lowerFriendly, $lowerExcluded)) continue;
    }

    if (!isset($groupedRawNames[$friendlyName])) {
        $groupedRawNames[$friendlyName] = [];
    }
    $groupedRawNames[$friendlyName][] = $rawName;
}

foreach ($groupedRawNames as $friendlyName => $rawNames) {
    $categories[] = [
        'id' => $friendlyName,
        'category' => $friendlyName,
        'name' => $friendlyName,
        'raw_name' => $rawNames[0]
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
