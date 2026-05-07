<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
restrictToAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error_msg'] = 'ID không hợp lệ';
    header('Location: ' . $base_url . 'admin/locked-users');
    exit();
}

$stmt = mysqli_prepare($conn, 'UPDATE users SET is_locked = 0, locked_at = NULL, locked_reason = NULL WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
if (mysqli_stmt_execute($stmt)) {
    // Không hiện thông báo thành công theo yêu cầu
} else {
    $_SESSION['error_msg'] = 'Lỗi: ' . mysqli_error($conn);
}
header('Location: ' . $base_url . 'admin/locked-users');
exit();
?>
