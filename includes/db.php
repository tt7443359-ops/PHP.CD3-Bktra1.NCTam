<?php
// Cấu hình
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đồng bộ timezone PHP = UTC+7
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
//Ghi nhớ
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Token CSRF 
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$host = "localhost";
$user = "Inori";
$pass = "inori123";
$dbname = "novel2x";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if ($conn) {
    mysqli_query($conn, "SET time_zone = '+07:00'"); // Đồng bộ timezone MySQL = UTC+7
    mysqli_set_charset($conn, 'utf8mb4');
}

// chứa Token
function csrf_tag() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

// Tự động kiểm tra CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Lỗi bảo mật (CSRF): Yêu cầu bị từ chối do thiếu hoặc sai mã Token. Vui lòng quay lại và thử lại.");
    }
}

$base_url = '/novel2x/';
$admin_email = "NCT@gmail.com";

// Đa ngôn ngữ
require_once __DIR__ . '/lang_manager.php';
?>