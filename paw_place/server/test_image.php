<?php
require_once __DIR__ . '/GrubhoundAPI.php';

$api = new GrubhoundAPI();
    // Pattern 1: With /grubhound/
    echo "Trying Pattern 1 (/grubhound/cafeteria/item-image/19627)..." . PHP_EOL;
    try {
        $data1 = $api->request('/cafeteria/item-image/19627');
        echo 'Success 1: ' . strlen($data1) . ' bytes' . PHP_EOL;
    } catch (Exception $e) { echo 'Error 1: ' . $e->getMessage() . PHP_EOL; }

    // Pattern 2: Without /grubhound/ (Directly under /api/)
    // We need to temporarily modify baseUrl or use a manual curl for this test
    echo "Trying Pattern 2 (/cafeteria/item-image/19627)..." . PHP_EOL;
    $url = "https://mis.foundationu.com/api/cafeteria/item-image/19627";
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
    $data2 = curl_exec($ch);
    $code2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($code2 === 200) {
        echo "Success 2: " . strlen($data2) . " bytes" . PHP_EOL;
    } else {
        echo "Error 2: HTTP $code2" . PHP_EOL;
        echo "Body 2: " . substr($data2, 0, 100) . PHP_EOL;
    }
?>
