<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
restrictToAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_user_id = $_SESSION['admin_id'] ?? 0;

if ($id <= 0 || $id == $current_user_id) {
    $_SESSION['error_msg'] = 'ID không hợp lệ hoặc không thể khóa chính mình';
    header('Location: ' . $base_url . 'admin/users');
    exit();
}

$stmt = mysqli_prepare($conn, 'UPDATE users SET is_locked = 1, locked_at = NOW(), locked_reason = ? WHERE id = ?');
$reason = 'Khóa bởi ad quản lý';
mysqli_stmt_bind_param($stmt, 'si', $reason, $id);
if (mysqli_stmt_execute($stmt)) {
    // Không hiện thông báo thành công theo yêu cầu
} else {
    $_SESSION['error_msg'] = 'Lỗi: ' . mysqli_error($conn);
}
header('Location: ' . $base_url . 'admin/users');
exit();
?>
