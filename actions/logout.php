<?php
require_once __DIR__ . '/../includes/db.php';

// Lưu giỏ hàng vào bảng users trước khi đăng xuất
if (isset($_SESSION['user'])) {
    $email = mysqli_real_escape_string($conn, $_SESSION['user']);

    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $cart_json = mysqli_real_escape_string($conn, json_encode($_SESSION['cart']));
        mysqli_query($conn, "UPDATE users SET cart_data = '$cart_json' WHERE email = '$email'");
    } else {
        mysqli_query($conn, "UPDATE users SET cart_data = NULL WHERE email = '$email'");
    }
}

// Ghi nhận thời điểm out thực tế → tự nhiên chờ 60 giây mới hiện "1 phút trước"
if (isset($_SESSION['user']) || isset($_SESSION['admin_email'])) {
    $current_email = $_SESSION['user'] ?? $_SESSION['admin_email'];
    $stmt_logout = mysqli_prepare($conn, "UPDATE users SET last_activity = NOW() WHERE email = ?");
    if ($stmt_logout) {
        mysqli_stmt_bind_param($stmt_logout, 's', $current_email);
        mysqli_stmt_execute($stmt_logout);
        mysqli_stmt_close($stmt_logout);
    }
}

$_SESSION = array();
session_destroy();

// Đẩy ngược 
$cookie_options = [
    'expires' => time() - 86400,
    'path' => '/',
    'samesite' => 'Lax',
    'httponly' => true
];
setcookie("stored_email", "", $cookie_options);
setcookie("stored_password", "", $cookie_options);
setcookie("stored_username", "", $cookie_options);
setcookie("stored_fullname", "", $cookie_options);

?>
<script>var REDIRECT_URL = "<?php echo $base_url; ?>";</script>
<script src="<?php echo $base_url; ?>public/assets/js/redirect.js"></script>
<?php
exit();
?>