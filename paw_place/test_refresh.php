<?php
require_once 'server/GrubhoundAPI.php';

echo "Testing Grubhound Token Refresh...\n";

try {
    $api = new GrubhoundAPI();
    
    // Config before
    $reflector = new ReflectionClass('GrubhoundAPI');
    $property = $reflector->getProperty('config');
    $property->setAccessible(true);
    $config = $property->getValue($api);
    echo "Current Token Ends: " . substr($config['access_token'], -10) . "\n";
    echo "Current Expires At: " . $config['expires_at'] . "\n";

    // Force Refresh
    $method = $reflector->getMethod('refreshToken');
    $method->setAccessible(true);
    $method->invoke($api);
    
    // Config after
    $configNew = $property->getValue($api);
    echo "New Token Ends: " . substr($configNew['access_token'], -10) . "\n";
    echo "New Expires At: " . $configNew['expires_at'] . "\n";
    
    if ($config['access_token'] !== $configNew['access_token']) {
        echo "SUCCESS: Token refreshed successfully!\n";
    } else {
        echo "WARNING: Token appears unchanged (API might return same token if valid).\n";
    }

} catch (Exception $e) {
    echo "ERROR: Refresh failed: " . $e->getMessage() . "\n";
}
?>
