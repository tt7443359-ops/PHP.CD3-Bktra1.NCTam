<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . "/../includes/db_product.php";

// Phân quyền dùng chung: Admin hoặc Khách
if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user']) && !isset($_COOKIE['stored_email'])) {
    header("Location: " . $base_url . "login");
    exit();
}

// Lấy ID sản phẩm
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kiểm tra ID và Stock
$check_sql = "SELECT id, stock_quantity FROM products WHERE id = $id";
$run_check = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($run_check) > 0) {
    if ($id > 0) {
        $row = mysqli_fetch_assoc($run_check);
        $stock = $row['stock_quantity'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        if (isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id] < $stock) {
                $_SESSION['cart'][$id]++;
            }
        } else {
            if ($stock > 0) {
                $_SESSION['cart'][$id] = 1;
            }
        }
    }
} else {
    header("Location: " . $base_url . "shop");
    exit();
}

// Quay lại trang trước hoặc đi đến giỏ hàng
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
if ($redirect === 'cart') {
    header("Location: " . $base_url . "cart");
} else {
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? $base_url . "shop"));
}
exit();