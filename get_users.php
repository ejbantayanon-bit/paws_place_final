<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "--- USERS TABLE SCHEMA ---\n";
$res = $conn->query("DESCRIBE users");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n--- USERS DATA ---\n";
$res = $conn->query("SELECT user_id, username, full_name, role, location_id, store_name FROM users");
if ($res) {
    while($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    // If location_id doesn't exist, just get base columns
    $res2 = $conn->query("SELECT user_id, username, full_name, role FROM users");
    while($row = $res2->fetch_assoc()) {
        print_r($row);
    }
}
$conn->close();
?>
