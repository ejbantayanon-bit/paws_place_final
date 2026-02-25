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
        'Cold Coffee' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee', 'Drinks'],
        'Milk Tea' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee'],
        'Specialty Drinks (Hot/Cold)' => ['Coffee And Milktea 2-paws Place', 'Milktea And Ice Coffee'],
        'Fruity Soda' => ['Drinks'],
        'Milk Drink (350ml)' => ['Drinks'],
        'Ice Cream in Cups (100g)' => ['Ice Cream'],
        'Ice Cream Bar (95g)' => ['Ice Cream'],
        'Snacks' => ['Snacks', 'Bread', 'Candy', 'Food'],
    ];

    // Use mapping if available, otherwise use the category name directly
    $targetCategories = isset($categoryMap[$category]) ? $categoryMap[$category] : [$category];
    $targetCategories = array_map('trim', $targetCategories);
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
            $apiPrice = 0;
            if (isset($item['price']) && (float)$item['price'] > 0) $apiPrice = (float)$item['price'];
            elseif (isset($item['item_price']) && (float)$item['item_price'] > 0) $apiPrice = (float)$item['item_price'];
            elseif (isset($item['unit_price']) && (float)$item['unit_price'] > 0) $apiPrice = (float)$item['unit_price'];
            
            // If API price is 0 or missing, try to extract from name (e.g. "Bread @ 10.00")
            if ($apiPrice <= 0) {
                $name = $item['name'] ?? '';
                $foundPrice = null;
                
                if (preg_match('/@\s*([\d,.]+)/', $name, $matches)) {
                    $foundPrice = (float)str_replace(',', '', $matches[1]);
                } elseif ($id && isset($localPrices[$id])) {
                    $foundPrice = $localPrices[$id];
                } else {
                    // Check by name (case-insensitive)
                    foreach ($localPrices as $key => $price) {
                        if (strcasecmp($key, $name) === 0) {
                            $foundPrice = $price;
                            break;
                        }
                    }
                }

                if ($foundPrice !== null) {
                    $item['price'] = $foundPrice;
                    $item['item_price'] = $foundPrice;
                } else {
                    $item['price'] = 0;
                    $item['item_price'] = 0;
                }
            } else {
                // Use API price
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
