<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';
restrictToAdmin();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: " . $base_url . "admin/dashboard");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    if ($id == 1) {
        $_SESSION['error_msg'] = "Không thể xóa danh mục mặc định!";
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
    }
}

header("Location: " . $base_url . "admin/categories");
exit();
?>
