<?php
// Run this script once to hash plaintext passwords in `users.password_hash`.
// Usage: php migrate_hash_passwords.php OR visit in browser.

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    die("DB connection failed: " . $conn->connect_error);
}

$res = $conn->query("SELECT user_id, username, password_hash FROM users");
$updated = 0;
$skipped = 0;
while ($row = $res->fetch_assoc()) {
    $uid = $row['user_id'];
    $stored = $row['password_hash'];
    // Check if it's already a bcrypt hash (starts with $2)
    if (strpos($stored, '$2') === 0) {
        $skipped++;
    } else {
        // Treat as plaintext -> hash and update
        $newHash = password_hash($stored, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->bind_param('si', $newHash, $uid);
        $stmt->execute();
        $stmt->close();
        $updated++;
    }
}
$res->free();
$conn->close();

if (php_sapi_name() === 'cli') {
    echo "Migration complete. Updated: $updated, Skipped: $skipped\n";
} else {
    echo "<p>Migration complete. Updated: $updated, Skipped: $skipped</p>";
}
?>