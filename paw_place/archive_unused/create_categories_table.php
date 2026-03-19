<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'categories' created or already exists.\n";
    
    // Check columns
    $result = $conn->query("SHOW COLUMNS FROM categories");
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . "\n";
    }
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
?>
