<?php
/**
 * Simple Database Backup Utility
 * Use this to export your tables to a .sql file once you gain access.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$backup_file = 'paws_place_backup_' . date('Y-m-d_H-i-s') . '.sql';

echo "Attempting to backup $DB_NAME to $backup_file ...\n";

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }

    $tables = array();
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $sql = "-- Paws Place Database Backup\n";
    $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";

    foreach ($tables as $table) {
        $result = $conn->query("SELECT * FROM $table");
        $num_fields = $result->field_count;

        $sql .= "DROP TABLE IF EXISTS `$table`;\n";
        $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
        $sql .= $row2[1] . ";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = $result->fetch_row()) {
                $sql .= "INSERT INTO `$table` VALUES(";
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $sql .= '"' . $row[$j] . '"';
                    } else {
                        $sql .= 'NULL';
                    }
                    if ($j < ($num_fields - 1)) {
                        $sql .= ',';
                    }
                }
                $sql .= ");\n";
            }
        }
        $sql .= "\n\n";
    }

    if (file_put_contents($backup_file, $sql)) {
        echo "Successfully backed up to $backup_file\n";
    } else {
        echo "Failed to write to file $backup_file\n";
    }

    $conn->close();
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}
?>
