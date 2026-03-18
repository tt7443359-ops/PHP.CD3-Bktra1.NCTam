<?php
session_start();

// Chặn cache
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

if (!isset($_COOKIE['stored_email']) && !isset($_SESSION['admin_logged_in'])) {
    header("Location: /PHP.CD3-Bktra1.Tam/login.php"); 
    exit();
}
?>