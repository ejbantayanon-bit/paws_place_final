<?php
/**
 * Proxy for MIS Cafeteria Item Images
 * Since MIS API requires Bearer Token, <img> tags cannot access it directly.
 * Instead, we use this endpoint: get_item_image.php?id={item_id}
 */
require_once __DIR__ . '/../GrubhoundAPI.php';

$itemId = $_GET['id'] ?? null;

if (!$itemId) {
    http_response_code(400);
    echo "Item ID is required";
    exit;
}

try {
    $configPath = __DIR__ . '/../config/grubhound_config.json';
    if (!file_exists($configPath)) {
        throw new Exception('Config missing');
    }
    $config = json_decode(file_get_contents($configPath), true);
    $token = $config['access_token'];

    // Direct endpoint as identified in testing
    $url = "https://mis.foundationu.com/api/cafeteria/item-image/" . urlencode($itemId);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $token,
            "Accept: image/*, */*"
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true
    ]);

    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($httpCode === 200) {
        if ($contentType) {
            header("Content-Type: " . $contentType);
        } else {
            // Fallback detection
            if (strpos($data, chr(0x89) . 'PNG') === 0) header("Content-Type: image/png");
            elseif (strpos($data, chr(0xFF) . chr(0xD8)) === 0) header("Content-Type: image/jpeg");
            elseif (strpos($data, 'GIF8') === 0) header("Content-Type: image/gif");
            else header("Content-Type: image/jpeg"); // Default
        }
        
        // Caching for performance (1 day)
        header("Cache-Control: public, max-age=86400");
        echo $data;
    } else {
        // Return a placeholder or just 404
        http_response_code($httpCode);
        // Optional: Output a placeholder 1x1 transparent pixel or default image
        echo $data; 
    }

} catch (Exception $e) {
    http_response_code(500);
    echo "Internal Server Error: " . $e->getMessage();
}
