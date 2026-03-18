<?php
$host = "localhost";
$user = "root";
$pass = "root"; 
$dbname = "shoplightnovel2x";

$conn = mysqli_connect($host, $user, $pass, $dbname);
$admin_email = "NCT@gmail.com";

if (!$conn) {
    die("Lỗi kết nối: " . mysqli_connect_error());
}
//

mysqli_set_charset($conn, "utf8mb4");
?>