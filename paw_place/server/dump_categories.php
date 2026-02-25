<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

$result = $conn->query("SELECT * FROM categories");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
file_put_contents(__DIR__ . '/categories_dump.txt', json_encode($data, JSON_PRETTY_PRINT));
echo "Categories dump saved to server/categories_dump.txt\n";
$conn->close();
?>
