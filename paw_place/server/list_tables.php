<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    echo $row[0] . "\n";
}
$conn->close();
?>
