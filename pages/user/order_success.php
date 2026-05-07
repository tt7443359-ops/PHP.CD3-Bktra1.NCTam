<?php
require_once __DIR__ . '/../../includes/db_product.php';
require_once __DIR__ . "/../../includes/auth_check.php";
require_once __DIR__ . "/../../includes/header.php";

$cart_items = [];
$grand_total = 0;

// Mua ngay
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT id, price, stock_quantity FROM products WHERE id = $id AND status='hiện'");
    $p = mysqli_fetch_assoc($res);
    if ($p) {
        $cart_items[] = ['product_id' => $p['id'], 'quantity' => 1, 'price' => $p['price']];
        $grand_total += $p['price'];
    }
} else {
    // Giỏ hàng
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        header("Location: " . $base_url . "shop");
        exit();
    }
    foreach ($_SESSION['cart'] as $id => $qty) {
        $res = mysqli_query($conn, "SELECT id, price, stock_quantity FROM products WHERE id = $id");
        $p = mysqli_fetch_assoc($res);
        if ($p) {
            $cart_items[] = ['product_id' => $p['id'], 'quantity' => $qty, 'price' => $p['price']];
            $grand_total += ($p['price'] * $qty);
        }
    }
}

if (empty($cart_items)) {
    header("Location: " . $base_url . "shop");
    exit();
}

$buyer_name = $_COOKIE['stored_fullname'] ?? ($_SESSION['username'] ?? "Khách ẩn danh");
$buyer_email = isset($_SESSION['user']) ? $_SESSION['user'] : ($_COOKIE['stored_email'] ?? "Không rõ");

// Lấy user_id nếu có
$user_id = 'NULL';
$safe_email = mysqli_real_escape_string($conn, $buyer_email);
$c_res = mysqli_query($conn, "SELECT id FROM users WHERE email = '$safe_email' LIMIT 1");
if ($c_row = mysqli_fetch_assoc($c_res)) {
    $user_id = $c_row['id'];
}

mysqli_begin_transaction($conn);
try {
    // 1.(Orders)
    if ($user_id != 'NULL') {
        $sql_order = "INSERT INTO orders (user_id, fullname_guest, email_guest, total_price, status) 
                          VALUES ($user_id, '$buyer_name', '$buyer_email', '$grand_total', 'chờ xác nhận')";
    } else {
        $sql_order = "INSERT INTO orders (fullname_guest, email_guest, total_price, status) 
                          VALUES ('$buyer_name', '$buyer_email', '$grand_total', 'chờ xác nhận')";
    }
    mysqli_query($conn, $sql_order);
    $order_id = mysqli_insert_id($conn);

    // 2. Lưu chi tiết đơn hàng (order_details) & Trừ số lượng kho
    foreach ($cart_items as $item) {
        $pid = $item['product_id'];
        $qty = $item['quantity'];
        $price = $item['price'];

        mysqli_query($conn, "INSERT INTO order_details (order_id, product_id, quantity, price_at_buy) VALUES ($order_id, $pid, $qty, $price)");

        // Trừ tồn kho
        mysqli_query($conn, "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - $qty) WHERE id = $pid");
    }

    mysqli_commit($conn);
    if (!isset($_GET['id']) && isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
} catch (Exception $e) {
    mysqli_rollback($conn);
    die("Lỗi CSDL: " . $e->getMessage());
}

header("Location: " . $base_url . "shop");
exit();
?>