<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// Ensure only Admin can add items (Basic check, can be enhanced)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn->set_charset('utf8mb4');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $name = $input['name'] ?? '';
    $category_id = intval($input['category_id'] ?? 0);
    $price = floatval($input['price'] ?? 0);
    $description = $input['description'] ?? '';
    $image_url = $input['image_url'] ?? 'assets/images/default_food.png';
    $is_available = isset($input['is_available']) ? intval($input['is_available']) : 1;

    if (empty($name) || $category_id <= 0 || $price < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        $conn->close();
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO menu_items (name, category_id, base_price, description, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sidssi', $name, $category_id, $price, $description, $image_url, $is_available);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu item added successfully', 'item_id' => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add item: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
