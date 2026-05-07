<?php

require_once __DIR__ . '/db.php';

// Nếu đã đăng nhập Ad/User, chặn truy cập vào các trang dành cho khách
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Admin
    header("Location: " . $base_url . "admin/dashboard");
    exit();
} elseif (isset($_SESSION['user'])) {
    //users
    header("Location: " . $base_url . "shop");
    exit();
}
?>
