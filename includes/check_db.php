<?php
include 'db.php';
$res = mysqli_query($conn, 'SELECT id, name, image_id FROM products LIMIT 10');
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | ImageID: " . $row['image_id'] . "\n";
}
?>
