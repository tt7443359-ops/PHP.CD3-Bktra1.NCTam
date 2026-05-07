<?php

require_once __DIR__ . '/db.php';

// 1. auth_check.php: Cập nhật hoạt động & Kiểm tra trạng thái khóa
if (isset($_SESSION['user']) || isset($_SESSION['admin_logged_in'])) {
    $current_email = $_SESSION['user'] ?? $_SESSION['admin_email'] ?? ''; // fallback to admin email if admin logged in
    
    if (!empty($current_email)) {
        // Cập nhật thời gian hoạt động cuối
        $stmt_act = mysqli_prepare($conn, "UPDATE users SET last_activity = NOW() WHERE email = ?");
        mysqli_stmt_bind_param($stmt_act, "s", $current_email);
        mysqli_stmt_execute($stmt_act);

        // Kiểm tra xem tài khoản có bị khóa trong DB không
        $stmt_lock = mysqli_prepare($conn, "SELECT is_locked FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt_lock, "s", $current_email);
        mysqli_stmt_execute($stmt_lock);
        $res_lock = mysqli_stmt_get_result($stmt_lock);
        $user_status = mysqli_fetch_assoc($res_lock);

        if ($user_status && $user_status['is_locked'] == 1) {
            // Nếu bị khóa, xóa session và đẩy về login
            session_unset();
            session_destroy();
            header("Location: " . $base_url . "login?error=locked");
            exit();
        }
    }
}

// Chặn truy cập nếu chưa đăng nhập
if (!isset($_SESSION['user']) && !isset($_SESSION['admin_logged_in'])) {
    header("Location: " . $base_url . "login");
    exit();
}

// Ktra quyền Ad
function restrictToAdmin()
{
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        global $base_url;
        header("Location: " . $base_url . "shop?error=unauthorized");
        exit();
    }
}
?>