<?php
// Generate fresh password hashes
$admin_password = 'admin123';
$cashier_password = 'cashier123';

echo "Generating new password hashes...\n\n";

$admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);
$cashier_hash = password_hash($cashier_password, PASSWORD_DEFAULT);

echo "Admin hash (length: " . strlen($admin_hash) . "):\n$admin_hash\n\n";
echo "Cashier hash (length: " . strlen($cashier_hash) . "):\n$cashier_hash\n\n";

// Test them
echo "Testing admin hash: " . (password_verify($admin_password, $admin_hash) ? "✓ PASS" : "✗ FAIL") . "\n";
echo "Testing cashier hash: " . (password_verify($cashier_password, $cashier_hash) ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Update database
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin01'");
$stmt->bind_param('s', $admin_hash);
if ($stmt->execute()) {
    echo "✓ Admin password updated in database\n";
} else {
    echo "✗ Failed to update admin password\n";
}
$stmt->close();

$stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = 'cashier01'");
$stmt->bind_param('s', $cashier_hash);
if ($stmt->execute()) {
    echo "✓ Cashier password updated in database\n";
} else {
    echo "✗ Failed to update cashier password\n";
}
$stmt->close();

// Verify database update
echo "\n=== Verification from Database ===\n";
$result = $conn->query("SELECT username, LENGTH(password_hash) as len, password_hash FROM users WHERE username IN ('admin01', 'cashier01')");
while ($row = $result->fetch_assoc()) {
    $test_pass = ($row['username'] === 'admin01') ? 'admin123' : 'cashier123';
    $verify = password_verify($test_pass, $row['password_hash']);
    echo $row['username'] . " (length: " . $row['len'] . "): " . ($verify ? "✓ VERIFIED" : "✗ FAILED") . "\n";
}

$conn->close();
echo "\nDone!\n";
?>
