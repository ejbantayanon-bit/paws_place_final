<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Add column if it doesn't exist
$sql = "ALTER TABLE users ADD COLUMN assigned_store VARCHAR(50) DEFAULT 'Paws Place'";
if ($conn->query($sql) === TRUE) {
    echo "Column assigned_store added successfully.\n";
} else {
    echo "Error adding column: " . $conn->error . "\n";
}

// Update existing users
$conn->query("UPDATE users SET assigned_store = 'Paws Place' WHERE assigned_store IS NULL");

// Default password (admin123 or similar, let's just use password for testing)
$pass = password_hash('password', PASSWORD_DEFAULT);

// Insert new cashiers
$stmt = $conn->prepare("INSERT IGNORE INTO users (username, password_hash, full_name, role, assigned_store) VALUES (?, ?, ?, 'Cashier', ?)");

$stores = [
    ['pupstop_cashier', $pass, 'Pup Stop Cashier', 'Pup Stop'],
    ['kennelmain_cashier', $pass, 'Kennel Main Cashier', 'Kennel Main'],
    ['kennelnorth_cashier', $pass, 'Kennel North Cashier', 'Kennel North']
];

foreach ($stores as $store) {
    $stmt->bind_param("ssss", $store[0], $store[1], $store[2], $store[3]);
    if ($stmt->execute()) {
        echo "Inserted basic cashier for " . $store[3] . "\n";
    }
}
$stmt->close();
$conn->close();
echo "Migration complete.\n";
?>
