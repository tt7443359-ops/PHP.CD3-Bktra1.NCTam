<?php
session_start();
require_once 'db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $new_pass = $_POST["new_password"];
    $confirm_pass = $_POST["confirm_password"];

    // Kiểm tra
    if ($new_pass !== $confirm_pass) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
    }
    else {
        // Kiểm tra sự tồn tại email
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

        if (mysqli_num_rows($check_email) > 0) {
            // Cập nhật mật khẩu mới
            // Lưu thẳng
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $safe_hashed_pass = mysqli_real_escape_string($conn, $hashed_pass);
            $sql_update = "UPDATE users SET password = '$safe_hashed_pass' WHERE email = '$email'";

            if (mysqli_query($conn, $sql_update)) {
                echo "<script>alert('Đặt lại mật khẩu thành công!'); window.location.href='login.php';</script>";
            }
            else {
                echo "<script>alert('Lỗi khi cập nhật CSDL!');</script>";
            }
        }
        else {
            echo "<script>alert('Email này không tồn tại trên hệ thống!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt Lại Mật Khẩu</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/reset-password.css">
</head>
<body>

<div class="container">
    <h2>Đặt Lại Mật Khẩu</h2>

    <form method="POST">
        <label>Email đăng ký</label>
        <input type="email" name="email" placeholder="Vui lòng nhập email" required>

        <label>Mật khẩu mới</label>
        <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required>

        <label>Xác nhận mật khẩu</label>
        <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>

        <button type="submit" class="btn">Cập Nhật Mật Khẩu</button>
    </form>

    <div class="links">
        <a href="login.php">Quay lại đăng nhập</a>
    </div>
</div>

</body>
</html>