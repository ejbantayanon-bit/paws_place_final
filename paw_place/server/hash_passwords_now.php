<?php
// Force hash all passwords NOW

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

while ($row = $res->fetch_assoc()) {
    $plaintext = $row['password_hash'];
    
    // Only hash if not already bcrypt
    if (strpos($plaintext, '$2') !== 0) {
        $newHash = password_hash($plaintext, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->bind_param('si', $newHash, $row['user_id']);
        
        if ($stmt->execute()) {
            echo "✓ Updated " . $row['username'] . " - Password hashed successfully<br>";
            $updated++;
        } else {
            echo "✗ Failed to update " . $row['username'] . "<br>";
        }
        $stmt->close();
    }
}

$res->free();
$conn->close();

echo "<br><strong>Total Updated: $updated</strong>";
?>
