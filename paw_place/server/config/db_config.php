<?php
// Database Configuration
// Local MySQL Server Configuration
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

// Create connection
function getDbConnection() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if ($conn->connect_errno) {
        return null;
    }
    
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>
