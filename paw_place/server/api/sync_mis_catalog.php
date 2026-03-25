<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn->set_charset('utf8mb4');

$locationWhitelists = [
    1 => ['Bread', 'Candy', 'Drinks', 'Food', 'Fruits', 'Ice Cream', 'Snacks', 'Supply'],
    2 => ['Bread', 'Candy', 'Drinks', 'Food', 'Fruits', 'Ice Cream', 'Snacks', 'Supply'],
    13 => ['Bread', 'Candy', 'Consignment', 'Drinks', 'Food', 'Fruits', 'Snacks']
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

$categoryMap = [
    'Coffee' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee'],
    'Milk Tea' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee'],
    'Drinks' => ['Drinks'], 
    'Fruits' => ['Fruits'],
    'Ice Cream' => ['Ice Cream', 'Ice Cream in Cups'],
    'Snacks' => ['Snacks', 'Candy'],
    'Food' => ['Food'],
    'Bread' => ['Bread'],
    'Consignment' => ['Consignment'],
    'Candy' => ['Candy'],
    'Supply' => ['Supply'],
];

// Load local prices
$localPrices = [];
$pricesPath = __DIR__ . '/../config/cafeteria_prices.json';
if (file_exists($pricesPath)) {
    $pricesData = json_decode(file_get_contents($pricesPath), true);
    if (isset($pricesData['items'])) {
        $localPrices = $pricesData['items'];
    }
}

// Load local icons
$localIcons = [];
$iconRes = $conn->query("SELECT name, icon FROM categories");
if ($iconRes) {
    while($row = $iconRes->fetch_assoc()) {
        $localIcons[$row['name']] = $row['icon'];
    }
}

$api = new GrubhoundAPI();
$report = [];

// Clean out old cache data to prevent stale ghost items (Optional, but safe since it fully rebuilds)
$conn->query("TRUNCATE TABLE api_cache_categories");
$conn->query("TRUNCATE TABLE api_cache_items");

foreach ($locationWhitelists as $locationId => $whitelist) {
    $result = $api->getCafeteriaCategories();
    
    $rawCategories = [];
    if (is_array($result) && isset($result['items'])) $rawCategories = $result['items'];
    elseif (is_array($result) && isset($result['data'])) $rawCategories = $result['data'];
    elseif (is_array($result) && !isset($result['success'])) $rawCategories = $result;

    $groupedRawNames = [];
    foreach ($rawCategories as $cat) {
        $rawName = is_string($cat) ? $cat : ($cat['name'] ?? $cat['category'] ?? '');
        $rawName = trim($rawName);
        if (!$rawName) continue;

        $friendlyName = $friendlyCategoryMap[$rawName] ?? $rawName;
        
        if (!in_array($friendlyName, $whitelist)) continue;

        if (!isset($groupedRawNames[$friendlyName])) {
            $groupedRawNames[$friendlyName] = [];
        }
        $groupedRawNames[$friendlyName][] = $rawName;
    }

    $c_stmt = $conn->prepare("INSERT INTO api_cache_categories (id, name, raw_name, icon, location_id, last_synced) VALUES (?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE name=VALUES(name), raw_name=VALUES(raw_name), icon=VALUES(icon), last_synced=NOW()");

    $catInserted = 0;
    $itemsInserted = 0;

    foreach ($groupedRawNames as $friendlyName => $rawNames) {
        $icon = isset($localIcons[$friendlyName]) ? $localIcons[$friendlyName] : '<i class="ph-duotone ph-fork-knife"></i>';
        
        // Save category
        $c_stmt->bind_param("ssssi", $friendlyName, $friendlyName, $rawNames[0], $icon, $locationId);
        $c_stmt->execute();
        $catInserted++;

        // Fetch items for this friendly category
        $targetCategories = isset($categoryMap[$friendlyName]) ? $categoryMap[$friendlyName] : [$friendlyName];
        $targetCategories = array_map('trim', $targetCategories);
        $allItems = [];

        foreach ($targetCategories as $misCat) {
            $iResult = $api->getCafeteriaItemsByCategoryLocation($locationId, $misCat);
            
            $items = [];
            if (is_array($iResult) && isset($iResult['items'])) $items = $iResult['items'];
            elseif (is_array($iResult) && isset($iResult['data'])) $items = $iResult['data'];
            elseif (is_array($iResult) && !isset($iResult['success'])) $items = $iResult;

            if (is_array($items)) {
                $allItems = array_merge($allItems, $items);
            }
        }

        // Deduplicate and process prices
        $unique = [];
        $i_stmt = $conn->prepare("INSERT INTO api_cache_items (item_id, name, category_name, price, location_id, last_synced) VALUES (?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE name=VALUES(name), price=VALUES(price), last_synced=NOW()");

        foreach ($allItems as $item) {
            $id = $item['id'] ?? $item['item_id'] ?? null;
            if (!$id) {
                // Generate a dummy ID if missing
                $id = md5($item['name'] . $friendlyName);
            }
            if (!isset($unique[$id])) {
                $unique[$id] = true;
                
                $apiPrice = 0;
                if (isset($item['price']) && (float)$item['price'] > 0) $apiPrice = (float)$item['price'];
                elseif (isset($item['item_price']) && (float)$item['item_price'] > 0) $apiPrice = (float)$item['item_price'];
                elseif (isset($item['unit_price']) && (float)$item['unit_price'] > 0) $apiPrice = (float)$item['unit_price'];
                
                if ($apiPrice <= 0) {
                    $name = $item['name'] ?? '';
                    $foundPrice = null;
                    if (preg_match('/@\s*([\d,.]+)/', $name, $matches)) {
                        $foundPrice = (float)str_replace(',', '', $matches[1]);
                    } elseif (isset($localPrices[$id])) {
                        $foundPrice = $localPrices[$id];
                    } else {
                        foreach ($localPrices as $key => $price) {
                            if (strcasecmp($key, $name) === 0) {
                                $foundPrice = $price;
                                break;
                            }
                        }
                    }
                    if ($foundPrice !== null) {
                        $apiPrice = $foundPrice;
                    }
                }
                
                $n = $item['name'] ?? 'Unknown Item';
                $i_stmt->bind_param("sssdi", $id, $n, $friendlyName, $apiPrice, $locationId);
                $i_stmt->execute();
                $itemsInserted++;
            }
        }
    }
    
    $report[$locationId] = [
        'categories_synced' => $catInserted,
        'items_synced' => $itemsInserted
    ];
}

echo json_encode([
    'success' => true,
    'message' => 'MIS Catalog successfully synchronized to local database.',
    'report' => $report
]);

$conn->close();
?>
