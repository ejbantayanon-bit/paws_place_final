<?php
header('Content-Type: application/json; charset=utf-8');
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

// GET /api/get_menu_items.php?category_id=N or all if not specified
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $includeHidden = isset($_GET['include_hidden']) && $_GET['include_hidden'] == '1';
    $categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
    
    if ($categoryId) {
        $sql = "SELECT item_id, name, category_id, base_price, is_available, image_url FROM menu_items WHERE category_id = ?";
        if (!$includeHidden) $sql .= " AND is_available = 1";
        $sql .= " ORDER BY name ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $categoryId);
    } else {
        $sql = "SELECT item_id, name, category_id, base_price, is_available, image_url FROM menu_items";
        if (!$includeHidden) $sql .= " WHERE is_available = 1";
        $sql .= " ORDER BY category_id ASC, name ASC";
        $stmt = $conn->prepare($sql);
    }
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $conn->error]);
        $conn->close();
        exit;
    }
    
    $stmt->execute();
    $res = $stmt->get_result();
    $items = [];
    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
    
    echo json_encode(['success' => true, 'items' => $items]);
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
$conn->close();
?>
