<?php
/**
 * Manually refresh Grubhound API token
 * Visit this file in browser to refresh the token
 * URL: http://localhost/paws_place_final/paw_place/server/refresh_grubhound_token.php
 */

require_once __DIR__ . '/GrubhoundAPI.php';

try {
    $api = new GrubhoundAPI();
    
    // Use reflection to call private method for testing
    $reflection = new ReflectionClass('GrubhoundAPI');
    $method = $reflection->getMethod('refreshToken');
    $method->setAccessible(true);
    $method->invoke($api);
    
    echo json_encode([
        'success' => true,
        'message' => 'Token refreshed successfully',
        'time' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'time' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
