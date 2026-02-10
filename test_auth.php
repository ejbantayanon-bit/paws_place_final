<?php
// Test password verification
$admin_hash = '$2y$10$wI07CGPPIzl1ajZpo3.NUnBMrPpoyk5u';
$cashier_hash = '$2y$10$UO.lPootpNxLRieV8pPal04aqopL3W/W';

echo "Testing admin123 against admin hash: ";
echo password_verify('admin123', $admin_hash) ? "✓ PASS\n" : "✗ FAIL\n";

echo "Testing cashier123 against cashier hash: ";
echo password_verify('cashier123', $cashier_hash) ? "✓ PASS\n" : "✗ FAIL\n";

// Now check what's actually in the database
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT username, password_hash FROM users WHERE username IN ('admin01', 'cashier01')");
echo "\n=== Database Check ===\n";
while ($row = $result->fetch_assoc()) {
    $username = $row['username'];
    $db_hash = $row['password_hash'];
    $test_password = ($username === 'admin01') ? 'admin123' : 'cashier123';
    
    echo "\n$username:\n";
    echo "  Hash length: " . strlen($db_hash) . " chars\n";
    echo "  Hash: " . $db_hash . "\n";
    echo "  Verify '$test_password': " . (password_verify($test_password, $db_hash) ? "✓ PASS" : "✗ FAIL") . "\n";
}

$conn->close();
?>
