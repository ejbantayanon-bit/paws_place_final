<?php
// require 'server/auth_check.php'; // Commented out for CLI execution

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    die('Connect Error: ' . $conn->connect_error);
}

echo "Connected successfully.\n";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'activity_logs'");
if ($result->num_rows == 0) {
    echo "Table 'activity_logs' DOES NOT EXIST!\n";
    exit;
} else {
    echo "Table 'activity_logs' exists.\n";
}

// Check count
$res = $conn->query("SELECT COUNT(*) as c FROM activity_logs");
if ($res) {
    $row = $res->fetch_assoc();
    echo "Row Count: " . $row['c'] . "\n";
} else {
    echo "Error counting rows: " . $conn->error . "\n";
}

// Describe table
echo "Schema:\n";
$desc = $conn->query("DESCRIBE activity_logs");
if ($desc) {
    while($d = $desc->fetch_assoc()) {
        echo $d['Field'] . " - " . $d['Type'] . "\n";
    }
} else {
    echo "Error describing table: " . $conn->error . "\n";
}

// Test API query logic (Join with users)
echo "\nTesting API Query Logic:\n";
$query = "
    SELECT l.log_id, l.user_id, l.user_role, l.activity_type, l.description, l.created_at, u.full_name, u.username 
    FROM activity_logs l 
    LEFT JOIN users u ON l.user_id = u.user_id 
    ORDER BY l.created_at DESC 
    LIMIT 5
";
$res = $conn->query($query);
if ($res) {
    echo "API Query successful. Fetched " . $res->num_rows . " rows.\n";
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows, JSON_PRETTY_PRINT);
} else {
    echo "API Query FAILED: " . $conn->error . "\n";
}

$conn->close();
?>
