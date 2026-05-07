<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
restrictToAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_user_id = $_SESSION['admin_id'] ?? 0;

if ($id <= 0 || $id == $current_user_id) {
    $_SESSION['error_msg'] = 'Không thể xóa chính mình hoặc ID không hợp lệ';
    header('Location: ' . $base_url . 'admin/locked-users');
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT id, avatar, is_locked FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) === 0) {
    $_SESSION['error_msg'] = 'User không tồn tại';
    header('Location: ' . $base_url . 'admin/locked-users');
    exit();
}
$user = mysqli_fetch_assoc($result);

if (!$user['is_locked']) {
    $_SESSION['error_msg'] = "Chỉ có thể xóa tài khoản đang bị khóa. Hãy khóa tài khoản trước.";
    header("Location: " . $base_url . "admin/users");
    exit();
}

// Xóa avatar
if (!empty($user['avatar']) && $user['avatar'] !== 'default_avatar.png') {
    $avatar_path = __DIR__ . '/../public/uploads/avatars/' . $user['avatar'];
    if (file_exists($avatar_path)) unlink($avatar_path);
}

// Xóa
$stmt_delete = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt_delete, "i", $id);
if (mysqli_stmt_execute($stmt_delete)) {
    // Không hiện thông báo thành công theo yêu cầu
} else {
    $_SESSION['error_msg'] = 'Lỗi: ' . mysqli_error($conn);
}
header('Location: ' . $base_url . 'admin/locked-users');
exit();
?>
