<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../auth_check.php';

if ($current_user_role !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$type = $input['type'] ?? '';

// For now, we store settings in a JSON file for simplicity and to avoid DB migrations
$settingsFile = __DIR__ . '/../config/shop_settings.json';
$settings = [];

if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true);
}

if ($type === 'save_general') {
    $settings['store_name'] = $input['store_name'] ?? 'PAWS PLACE';
    $settings['welcome_message'] = $input['welcome_message'] ?? '';
} elseif ($type === 'save_hours') {
    $settings['open_time'] = $input['open_time'] ?? '08:00';
    $settings['close_time'] = $input['close_time'] ?? '18:00';
} elseif ($type === 'get') {
    echo json_encode(['success' => true, 'settings' => $settings]);
    exit;
}

file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
