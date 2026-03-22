<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Base URL
$base_url = '/PHP.CD3-Bktra1.Tam/';

$total_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $total_items += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($page_title) ? $page_title : "ShopLIGHTNOVEL2X"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>logo.css">
    
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .common-header {
            background: linear-gradient(75deg, #d52b1e, #7551d8, #64c5c5);
            padding: 5px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            width: 100%;
        }

        .common-header nav a {
            margin-left: 20px;
            text-decoration: none;
            color: #e7e2e2;
            font-weight: 500;
            transition: color 0.3s;
        }

        .common-header nav a:hover {
            color: #182230;
        }
    </style>
</head>
<body>
<header class="common-header">
    <div class="logo-container">
        <a href="<?php echo $base_url; ?>index.php">
            <img src="<?php echo $base_url; ?>img/images.jpg" alt="Logo">
        </a>
    </div>
    <nav>
        <?php
$is_guest_user = isset($_SESSION['user']) || isset($_COOKIE['stored_email']);
$is_admin = isset($_SESSION['admin_logged_in']);
$is_logged_in = $is_guest_user || $is_admin;
?>

        <?php if ($is_guest_user): ?>
            <a href="<?php echo $base_url; ?>products/view_cart.php" style="position: relative; margin-right: 15px;">
                🛒 Giỏ hàng
                <?php if ($total_items > 0): ?>
                    <span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; position: absolute; top: -10px; right: -15px; font-weight: bold;">
                        <?php echo $total_items; ?>
                    </span>
                <?php
    endif; ?>
            </a>
        <?php
endif; ?>
        
        <?php if ($is_admin): ?>
            <a href="<?php echo $base_url; ?>dashboard.php">Dashboard</a>
            <a href="<?php echo $base_url; ?>admin_messages.php">Phản Hồi</a>
            <a href="<?php echo $base_url; ?>products/admin_products.php">Quản Lý Sản Phẩm</a>
        <?php
endif; ?>
        
        <a href="<?php echo $base_url; ?>index.php">Trang Chủ</a>
        <a href="<?php echo $base_url; ?>products/index1.php">Sản phẩm</a>
        <a href="<?php echo $base_url; ?>contact.php">Liên hệ</a>

        <?php if ($is_logged_in): ?>
            <a href="<?php echo $base_url; ?>logout.php">Đăng Xuất</a>
        <?php
endif; ?>
    </nav>
</header>
