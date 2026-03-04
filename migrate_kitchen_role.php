<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "Updating users table role enum...\n";
// Add 'Kitchen' to the role enum
$sql = "ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'Cashier', 'Barista', 'Kitchen') NOT NULL";
if ($conn->query($sql) === TRUE) {
    echo "Role enum updated successfully.\n";
} else {
    echo "Error updating role enum: " . $conn->error . "\n";
}

// Create kitchen staff user
$username = 'kitchen_staff';
$password = password_hash('password', PASSWORD_DEFAULT);
$full_name = 'Kitchen Staff';
$role = 'Kitchen';
$store = 'Paws Place';

$check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
    echo "Creating kitchen staff user...\n";
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, full_name, role, assigned_store) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password, $full_name, $role, $store);
    if ($stmt->execute()) {
        echo "Kitchen staff user created successfully.\n";
    } else {
        echo "Error creating user: " . $conn->error . "\n";
    }
    $stmt->close();
} else {
    echo "Kitchen staff user already exists.\n";
}

$check->close();
$conn->close();
echo "Migration complete.\n";
?>
