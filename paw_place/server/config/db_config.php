<?php
// Database Configuration
// Remote MySQL Server Configuration
$DB_HOST = '172.16.50.93';
$DB_USER = 'paws_user';
$DB_PASS = 'Cy123';
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
