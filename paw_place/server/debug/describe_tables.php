<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$tables = ['activity_logs', 'users'];
foreach ($tables as $table) {
    echo "Table: $table\n";
    $result = $conn->query("DESCRIBE $table");
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    echo "\n";
}
$conn->close();
?>
