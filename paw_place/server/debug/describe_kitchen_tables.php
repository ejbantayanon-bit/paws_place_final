<?php
$conn = new mysqli('localhost', 'root', '', 'paws_place_db');
$tables = ['menu_items', 'orders', 'order_items', 'categories'];
foreach($tables as $t) {
    echo "Table: $t\n";
    $res = $conn->query("DESCRIBE $t");
    while($r = $res->fetch_assoc()) echo $r['Field']." ".$r['Type']."\n";
    echo "\n";
}
?>
