<?php
session_start();
$_SESSION = array();
session_destroy();

// Đẩy ngược
setcookie("stored_email", "", time() - 86400, "/");
setcookie("stored_password", "", time() - 86400, "/");
setcookie("stored_username", "", time() - 86400, "/");

echo "<script>window.location.replace('index.php');</script>";
exit();
?>