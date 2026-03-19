<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
$res = $conn->query("SELECT username, full_name, role FROM users WHERE role = 'Kitchen'");
while($r = $res->fetch_assoc()) {
    echo "Username: " . $r['username'] . " | Name: " . $r['full_name'] . "\n";
}
?>
