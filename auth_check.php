<?php
session_start();

if (!isset($_SESSION['user']) && !isset($_SESSION['admin_logged_in']) && !isset($_COOKIE['stored_email'])) {
    header("Location: /PHP.CD3-Bktra1.Tam/login.php"); 
    exit();
}

// Nếu có Cookie nhưng mất Session, có thể tự động khôi phục Session
if (!isset($_SESSION['user']) && isset($_COOKIE['stored_email'])) {
    $_SESSION['user'] = $_COOKIE['stored_email'];
}
?>