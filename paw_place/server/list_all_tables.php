<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
$res = $conn->query("SHOW TABLES");
while($r = $res->fetch_array()) echo $r[0]."\n";
?>
