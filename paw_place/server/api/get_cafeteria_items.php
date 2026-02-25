<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

$category = $_GET['category'] ?? null;
$locationId = $_GET['location_id'] ?? null;

if (!$category) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'category parameter is required']);
    exit;
}

try {
    $api = new GrubhoundAPI();

    // Map local category names to MIS API category names
    $categoryMap = [
        'Hot Coffee' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee'],
        'Cold Coffee' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee'],
        'Milk Tea' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee'],
        'Specialty Drinks (Hot/Cold)' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee'],
        'Fruity Soda' => ['Drinks'],
        'Milk Drink (350ml)' => ['Drinks'],
        'Ice Cream in Cups (100g)' => ['Ice Cream'],
        'Ice Cream Bar (95g)' => ['Ice Cream'],
        'Snacks' => ['Snacks', 'Bread', 'Candy', 'Food'],
    ];

    $targetCategories = isset($categoryMap[$category]) ? $categoryMap[$category] : [$category];
    $allItems = [];

    foreach ($targetCategories as $misCat) {
        if ($locationId) {
            $result = $api->getCafeteriaItemsByCategoryLocation($locationId, $misCat);
        } else {
            $result = $api->getCafeteriaItemsByCategory($misCat);
        }

        $items = [];
        if (is_array($result) && isset($result['items'])) {
            $items = $result['items'];
        } elseif (is_array($result) && isset($result['data'])) {
            $items = $result['data'];
        } elseif (is_array($result) && !isset($result['success'])) {
            // Raw array return
            $items = $result;
        }

        if (is_array($items)) {
            $allItems = array_merge($allItems, $items);
        }
    }
    
    $items = $allItems;

    // Remove duplicates by ID
    $unique = [];
    $filtered = [];
    foreach ($items as $it) {
        $id = $it['id'] ?? $it['item_id'] ?? null;
        if ($id && !isset($unique[$id])) {
            $unique[$id] = true;
            $filtered[] = $it;
        }
    }
    $items = $filtered;

    // Load local price mapping if exists
    $localPrices = [];
    $pricesPath = __DIR__ . '/../config/cafeteria_prices.json';
    if (file_exists($pricesPath)) {
        $pricesData = json_decode(file_get_contents($pricesPath), true);
        if (isset($pricesData['items'])) {
            $localPrices = $pricesData['items'];
        }
    }

    // Merge prices
    if (is_array($items)) {
        foreach ($items as &$item) {
            $id = $item['id'] ?? $item['item_id'] ?? null;
            $apiPrice = isset($item['price']) ? (float)$item['price'] : (isset($item['item_price']) ? (float)$item['item_price'] : 0);
            
            // If API price is 0 or missing, check local mapping
            if (($apiPrice <= 0) && $id && isset($localPrices[$id])) {
                $item['price'] = $localPrices[$id];
                $item['item_price'] = $localPrices[$id];
            } else {
                // Ensure consistent fields
                $item['price'] = $apiPrice;
                $item['item_price'] = $apiPrice;
            }
        }
    }

    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
