<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Kitchen', 'Barista'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$store = $data['store'] ?? '';

$allowed_stores = ['Paws Place', 'Pup Stop', 'Kennel Main', 'Kennel North', 'All'];

if (!in_array($store, $allowed_stores)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid store selected']);
    exit;
}

$_SESSION['assigned_store'] = $store;

echo json_encode(['success' => true, 'store' => $store]);
?>
