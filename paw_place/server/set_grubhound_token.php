<?php
/**
 * Local helper to set Grubhound tokens in config file.
 * Usage: POST JSON { access_token, refresh_token, expires_at }
 * Restriction: only callable from localhost for safety.
 */
header('Content-Type: application/json; charset=utf-8');

$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!in_array($clientIp, ['127.0.0.1', '::1', 'localhost'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: local access only', 'client_ip' => $clientIp]);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload || !is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
    exit;
}

$required = ['access_token', 'refresh_token', 'expires_at'];
foreach ($required as $r) {
    if (empty($payload[$r])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $r"]);
        exit;
    }
}

$configPath = __DIR__ . '/config/grubhound_config.json';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Config file not found: ' . $configPath]);
    exit;
}

$config = json_decode(file_get_contents($configPath), true);
if (!is_array($config)) $config = [];

$config['access_token'] = $payload['access_token'];
$config['refresh_token'] = $payload['refresh_token'];
$config['expires_at'] = $payload['expires_at'];

if (file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to write config file']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Tokens updated', 'expires_at' => $config['expires_at']]);
exit;
?>
