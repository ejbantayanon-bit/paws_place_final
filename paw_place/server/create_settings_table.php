<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'settings' created successfully\n";
    // Insert default settings if empty
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
        ('maintenance_mode', 'false'),
        ('store_name', 'GrubHound'),
        ('tax_rate', '0.00'),
        ('service_charge', '0.00')
    ");
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
