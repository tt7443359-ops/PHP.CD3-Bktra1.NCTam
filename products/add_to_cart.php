<?php
session_start();
require_once("../auth_check.php");
require_once("db_product.php");

// Phân quyền dùng chung: Admin hoặc Khách
if (!isset($_SESSION['admin_logged_in']) && !isset($_COOKIE['stored_email'])) {
    header("Location: ../login.php");
    exit();
}

// Lấy ID sản phẩm
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kiểm tra ID
$check_sql = "SELECT id FROM products WHERE id = $id";
$run_check = mysqli_query($conn, $check_sql);

if(mysqli_num_rows($run_check) > 0) {
    if ($id > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }
    }
} else {
    header("Location: index1.php?error=invalid_id");
    exit();
}

// Quay lại trang trước
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();