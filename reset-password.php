<?php
session_start();
require_once 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $new_pass = $_POST["new_password"];
    $confirm_pass = $_POST["confirm_password"];

    // Kiểm tra 2 mật khẩu có khớp không
    if ($new_pass !== $confirm_pass) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
    } else {
        // Kiểm tra email có tồn tại trong hệ thống không
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        
        if (mysqli_num_rows($check_email) > 0) {
            // Cập nhật mật khẩu mới
            // Lưu thẳng
            $safe_new_pass = mysqli_real_escape_string($conn, $new_pass);
            $sql_update = "UPDATE users SET password = '$safe_new_pass' WHERE email = '$email'";
            
            if (mysqli_query($conn, $sql_update)) {
                echo "<script>alert('Đặt lại mật khẩu thành công!'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Lỗi khi cập nhật CSDL!');</script>";
            }
        } else {
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
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family: Arial, Helvetica, sans-serif; }
    body { height:100vh; display:flex; justify-content:center; align-items:center; background: linear-gradient(to bottom,#f0fcf9, #e6f0ff); }
    .container { background:#fff; width:500px; padding:40px; border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,0.15); }
    h2 { text-align:center; margin-bottom:30px; font-size:28px; color: #333; }
    label { display:block; margin-bottom:5px; font-weight:600; font-size:14px; color: #555; }
    input { width:100%; padding:14px; border-radius:10px; border:1px solid #ddd; font-size:15px; margin-bottom:15px; }
    input:focus { outline:none; border-color:#4f46e5; }
    .btn { width:100%; padding:16px; border:none; border-radius:12px; background:#4f46e5; color:white; font-size:16px; font-weight:bold; cursor:pointer; transition: 0.3s; }
    .btn:hover { background:#4338ca; transform: translateY(-2px); }
    .links { text-align:center; margin-top:20px; font-size:14px; }
    .links a { color:#4f46e5; text-decoration:none; font-weight:600; }
    .links a:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="container">
    <h2>Đặt Lại Mật Khẩu</h2>

    <form method="POST">
        <label>Email đăng ký</label>
        <input type="email" name="email" placeholder="Nhập email của ông" required>

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