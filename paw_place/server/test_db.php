<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

echo "Attempting to connect to $DB_HOST ... \n";

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . " (Error No: " . $conn->connect_errno . ")\n");
    }
    echo "Connected successfully to database: $DB_NAME\n";
    
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "Tables in database:\n";
        while ($row = $result->fetch_array()) {
            echo " - " . $row[0] . "\n";
        }
    } else {
        echo "Could not list tables: " . $conn->error . "\n";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}
?>
