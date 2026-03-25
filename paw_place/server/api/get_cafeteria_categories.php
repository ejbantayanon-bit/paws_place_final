<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

// Categories that are NOT food/drink related
$excludedCategories = [
    'Optimum Cash Incentives','Catering','Room Rental','School','School Supply',
    'School Uniform','Spray','Supply','Vending Machine','Other','Miscellaneous',
    'Kit','Inventory Kit','Kit Items','Kit Components','Uniform',
    'School Uniform','Medicine','Personal Care','Cleaning'
];

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

$locationWhitelists = [
    1 => ['Bread', 'Candy', 'Drinks', 'Food', 'Fruits', 'Ice Cream', 'Snacks', 'Supply'],
    2 => ['Bread', 'Candy', 'Drinks', 'Food', 'Fruits', 'Ice Cream', 'Snacks', 'Supply'],
    13 => ['Bread', 'Candy', 'Consignment', 'Drinks', 'Food', 'Fruits', 'Snacks']
];

$locationIdParam = $_GET['location_id'] ?? '';
$locationId = ($locationIdParam !== '') ? (int)$locationIdParam : null;

$categories = null;
$error = null;

try {
    $api = new GrubhoundAPI();
    $result = @$api->getCafeteriaCategories();
    
    if (!$result || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
        throw new Exception("Live MIS API returned failure or took too long.");
    }
    
    $rawCategories = [];
    if (is_array($result) && isset($result['items'])) $rawCategories = $result['items'];
    elseif (is_array($result) && isset($result['data'])) $rawCategories = $result['data'];
    elseif (is_array($result) && !isset($result['success'])) $rawCategories = $result;

    if (empty($rawCategories)) {
        throw new Exception("Received empty categories array from live MIS API.");
    }

    $groupedRawNames = [];
    foreach ($rawCategories as $cat) {
        $rawName = is_string($cat) ? $cat : ($cat['name'] ?? $cat['category'] ?? '');
        $rawName = trim($rawName);
        if (!$rawName) continue;

        $friendlyName = $friendlyCategoryMap[$rawName] ?? $rawName;
        
        if ($locationId !== null && isset($locationWhitelists[$locationId])) {
            if (!in_array($friendlyName, $locationWhitelists[$locationId])) continue;
        } else {
            $lowerRawName = strtolower($rawName);
            $lowerExcluded = array_map('strtolower', $excludedCategories);
            if (in_array($lowerRawName, $lowerExcluded)) continue;
            
            $lowerFriendly = strtolower($friendlyName);
            if (in_array($lowerFriendly, $lowerExcluded)) continue;
        }

        if (!isset($groupedRawNames[$friendlyName])) {
            $groupedRawNames[$friendlyName] = [];
        }
        $groupedRawNames[$friendlyName][] = $rawName;
    }

    // Pull local icons exclusively
    $localIcons = [];
    $conn = new mysqli('localhost', 'root', '', 'paws_place_db');
    if (!$conn->connect_errno) {
        $conn->set_charset('utf8mb4');
        $iconRes = $conn->query("SELECT name, icon FROM categories");
        if ($iconRes) {
            while($row = $iconRes->fetch_assoc()) {
                $localIcons[$row['name']] = $row['icon'];
            }
        }
        $conn->close();
    }

    $categories = [];
    foreach ($groupedRawNames as $friendlyName => $rawNames) {
        $icon = isset($localIcons[$friendlyName]) ? $localIcons[$friendlyName] : '<i class="ph-duotone ph-fork-knife"></i>';
        $categories[] = [
            'id' => $friendlyName,
            'category' => $friendlyName,
            'name' => $friendlyName,
            'raw_name' => $rawNames[0],
            'icon' => $icon
        ];
    }
    
    usort($categories, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

} catch (Exception $e) {
    // -------------------------------------------------------------
    // OFFLINE FALLBACK
    // -------------------------------------------------------------
    $error = $e->getMessage();
    try {
        $conn = new mysqli('localhost', 'root', '', 'paws_place_db');
        if ($conn->connect_errno) throw new Exception("Database connection failed");
        $conn->set_charset('utf8mb4');

        if ($locationId !== null) {
            $stmt = $conn->prepare("SELECT id, name, raw_name, icon FROM api_cache_categories WHERE location_id = ? ORDER BY name ASC");
            $stmt->bind_param('i', $locationId);
        } else {
            $stmt = $conn->prepare("SELECT id, name, ANY_VALUE(raw_name) as raw_name, ANY_VALUE(icon) as icon FROM api_cache_categories GROUP BY id, name ORDER BY name ASC");
        }
        
        $stmt->execute();
        $res = $stmt->get_result();
        
        $categories = [];
        while ($row = $res->fetch_assoc()) {
            $categories[] = [
                'id' => $row['id'],
                'category' => $row['name'],
                'name' => $row['name'],
                'raw_name' => $row['raw_name'],
                'icon' => $row['icon']
            ];
        }
        $stmt->close();
        $conn->close();
    } catch (Exception $fallback_e) {
        echo json_encode(['success' => false, 'message' => "Both Live MIS and Offline DB failed: " . $fallback_e->getMessage()]);
        exit;
    }
}

echo json_encode([
    'success' => true, 
    'is_offline' => ($error !== null), 
    'error_log' => $error, 
    'categories' => $categories
]);
?>
