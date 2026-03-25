<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

$category = $_GET['category'] ?? null;
$locationIdParam = $_GET['location_id'] ?? '';
$locationId = ($locationIdParam !== '') ? (int)$locationIdParam : null;

if (!$category) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'category parameter is required']);
    exit;
}

$items = null;
$error = null;

try {
    $api = new GrubhoundAPI();

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

    $targetCategories = isset($categoryMap[$category]) ? $categoryMap[$category] : [$category];
    $targetCategories = array_map('trim', $targetCategories);
    $allItems = [];

    $liveSuccess = false;

    foreach ($targetCategories as $misCat) {
        if ($locationId) {
            $result = @$api->getCafeteriaItemsByCategoryLocation($locationId, $misCat);
        } else {
            $result = @$api->getCafeteriaItemsByCategory($misCat);
        }

        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
             continue;
        }

        $catItems = [];
        if (is_array($result) && isset($result['items'])) $catItems = $result['items'];
        elseif (is_array($result) && isset($result['data'])) $catItems = $result['data'];
        elseif (is_array($result) && !isset($result['success'])) $catItems = $result;

        if (is_array($catItems) && count($catItems) > 0) {
            $allItems = array_merge($allItems, $catItems);
            $liveSuccess = true;
        }
    }

    if (!$liveSuccess || empty($allItems)) {
        throw new Exception("Live MIS API returned empty or failed for category.");
    }

    // Filter Duplicates
    $unique = [];
    $filtered = [];
    foreach ($allItems as $it) {
        $id = $it['id'] ?? $it['item_id'] ?? null;
        if ($id && !isset($unique[$id])) {
            $unique[$id] = true;
            $filtered[] = $it;
        }
    }
    
    // Load local manual price overrides mapping
    $localPrices = [];
    $pricesPath = __DIR__ . '/../config/cafeteria_prices.json';
    if (file_exists($pricesPath)) {
        $pricesData = json_decode(file_get_contents($pricesPath), true);
        if (isset($pricesData['items'])) $localPrices = $pricesData['items'];
    }

    $items = [];
    foreach ($filtered as $item) {
        $id = $item['id'] ?? $item['item_id'] ?? null;
        $apiPrice = 0;
        if (isset($item['price']) && (float)$item['price'] > 0) $apiPrice = (float)$item['price'];
        elseif (isset($item['item_price']) && (float)$item['item_price'] > 0) $apiPrice = (float)$item['item_price'];
        elseif (isset($item['unit_price']) && (float)$item['unit_price'] > 0) $apiPrice = (float)$item['unit_price'];
        
        if ($apiPrice <= 0) {
            $name = $item['name'] ?? '';
            $foundPrice = null;
            if (preg_match('/@\s*([\d,.]+)/', $name, $matches)) {
                $foundPrice = (float)str_replace(',', '', $matches[1]);
            } elseif ($id && isset($localPrices[$id])) {
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
        
        $items[] = [
            'id' => $id,
            'item_id' => $id,
            'name' => $item['name'] ?? 'Unknown',
            'price' => $apiPrice,
            'item_price' => $apiPrice
        ];
    }

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
            $stmt = $conn->prepare("SELECT item_id, name, price FROM api_cache_items WHERE category_name = ? AND location_id = ? ORDER BY name ASC");
            $stmt->bind_param('si', $category, $locationId);
        } else {
            $stmt = $conn->prepare("SELECT item_id, name, ANY_VALUE(price) as price FROM api_cache_items WHERE category_name = ? GROUP BY item_id, name ORDER BY name ASC");
            $stmt->bind_param('s', $category);
        }
        
        $stmt->execute();
        $res = $stmt->get_result();
        
        $items = [];
        while ($row = $res->fetch_assoc()) {
            $items[] = [
                'id' => $row['item_id'],
                'item_id' => $row['item_id'],
                'name' => $row['name'],
                'price' => (float)$row['price'],
                'item_price' => (float)$row['price']
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
    'items' => $items
]);
?>
