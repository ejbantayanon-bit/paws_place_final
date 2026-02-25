<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

// Categories that are NOT food/drink related — exclude from the ordering page
$excludedCategories = [
    'Optimum Cash Incentives',
    'Catering',
    'Consignment',
    'Room Rental',
    'School',
    'School Supply',
    'School Uniform',
    'Spray',
    'Supply',
    'Vending Machine',
];

try {
    $DB_HOST = 'localhost';
    $DB_USER = 'root';
    $DB_PASS = '';
    $DB_NAME = 'paws_place_db';

    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_errno) {
        throw new Exception('Database connection failed');
    }
    $conn->set_charset('utf8mb4');

    $sql = "SELECT category_id, name FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
    $result = $conn->query($sql);
    $categories = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = [
                'id' => $row['category_id'],
                'category' => $row['name'],
                'name' => $row['name']
            ];
        }
    }
    $conn->close();
    
    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
