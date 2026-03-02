<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/GrubhoundAPI.php';

$api = new GrubhoundAPI();
$categories = ['Drinks', 'Food', 'Snacks', 'Bread'];

foreach ($categories as $cat) {
    echo "Searching in $cat..." . PHP_EOL;
    try {
        $res = $api->getCafeteriaItemsByCategory($cat);
        $items = $res['items'] ?? $res['data'] ?? $res ?? [];
        foreach ($items as $item) {
            // Check if there's any hint of an image or just try the first 5 items
            if (isset($item['item_id'])) {
                $id = $item['item_id'];
                $url = "https://mis.foundationu.com/api/cafeteria/item-image/$id";
                $config = json_decode(file_get_contents(__DIR__ . '/config/grubhound_config.json'), true);
                $token = $config['access_token'];
                
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"],
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ]);
                $data = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($code === 200) {
                    echo "FOUND IMAGE for item [{$item['name']}] (ID: $id)!" . PHP_EOL;
                    exit;
                }
            }
        }
    } catch (Exception $e) { }
}
echo "No images found in checked categories." . PHP_EOL;
?>
