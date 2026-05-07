<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . $base_url . "login");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    $custom_reason = trim($_POST['custom_reason'] ?? '');

    $final_reason = ($reason === 'Lý do khác') ? $custom_reason : $reason;
    $final_reason = mysqli_real_escape_string($conn, $final_reason);

    if ($order_id <= 0 || empty($final_reason)) {
        header("Location: " . $base_url . "order-history?cancel_err=1");
        exit();
    }

    // Xác minh đơn thuộc về user đang đăng nhập
    $email = mysqli_real_escape_string($conn, $_SESSION['user']);
    $u_res = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
    $u_row = mysqli_fetch_assoc($u_res);
    if (!$u_row) {
        header("Location: " . $base_url . "order-history?cancel_err=2");
        exit();
    }

    $uid = $u_row['id'];
    $o_res = mysqli_query($conn, "SELECT id, status FROM orders WHERE id=$order_id AND user_id=$uid LIMIT 1");
    $order = mysqli_fetch_assoc($o_res);

    if (!$order || !in_array($order['status'], ['chờ xác nhận', 'đã xác nhận'])) {
        header("Location: " . $base_url . "order-history?cancel_err=3");
        exit();
    }

    mysqli_query($conn, "UPDATE orders SET cancel_request=1, cancel_reason='$final_reason' WHERE id=$order_id");
    header("Location: " . $base_url . "order-history?cancel_ok=1");
    exit();
}

header("Location: " . $base_url . "order-history");
exit();
?>