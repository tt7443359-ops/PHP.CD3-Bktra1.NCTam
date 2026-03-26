<?php
require_once("db.php"); 

$errors = [];
$username = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // 1. Kiểm tra các trường trống và định dạng
    if (empty($username)) $errors['username'] = "Vui lòng nhập họ tên.";
    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không đúng định dạng.";
    }

    // 2. Chặn trùng mail
    if (empty($errors)) {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $check_mail = "SELECT id FROM users WHERE email = '$safe_email' LIMIT 1";
        $result = mysqli_query($conn, $check_mail);
        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = "Email này đã được đăng ký. Vui lòng dùng mail khác!";
        }
    }

    // 3. Kiểm tra Mật khẩu
    if (empty($password)) {
        $errors['password'] = "Vui lòng nhập mật khẩu.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }
    if ($password !== $confirm) {
        $errors['confirm_password'] = "Mật khẩu xác nhận không khớp.";
    }

    // 4. Lưu vào csdl và cookie
    if (empty($errors)) {
        $ip = $_SERVER['REMOTE_ADDR']; // Lấy ip users
        $safe_username = mysqli_real_escape_string($conn, $username);
        $safe_email = mysqli_real_escape_string($conn, $email);
        $safe_password = mysqli_real_escape_string($conn, $password);
        $safe_ip = mysqli_real_escape_string($conn, $ip);
        // Lưu vào bảng users
        $sql = "INSERT INTO users (fullname, email, password, ip_address) VALUES ('$safe_username', '$safe_email', '$safe_password', '$safe_ip')";
        
        if (mysqli_query($conn, $sql)) {
            // Set Cookie
            setcookie("stored_username", $username, time() + 86400, "/");
            setcookie("stored_email", $email, time() + 86400, "/");
            setcookie("stored_password", $password, time() + 86400, "/");

            header("Location: login.php?msg=success");
            exit();
        } else {
            $errors['system'] = "Lỗi hệ thống, vui lòng thử lại!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="stylesheet" href="css/register.css">
</head>
<body>
      
<div class="container fade-in-down">
    <h2>Đăng Ký</h2>
     <?php if  (!empty($success_msg)): ?>
         <p class="success-msg"><?php echo $success_msg; ?></p>
     <?php endif; ?>

    <form action="" method="POST">
        <label>Họ và Tên</label>
        <input type="text" name="username" placeholder="Nhập họ tên" required>
        <?php if(isset($errors['username'])): ?> <span class="error-msg"><?php echo $errors['username']; ?></span> <?php endif; ?>

        <label>Email</label>
        <input type="email" name="email" placeholder="Nhập email" required>
        <?php if(isset($errors['email'])): ?> <span class="error-msg"><?php echo $errors['email']; ?></span> <?php endif; ?>

        <label>Mật khẩu</label>
        <input type="password" name="password" placeholder="Nhập mật khẩu" required>
         <?php if(isset($errors['password'])): ?> <span class="error-msg"><?php echo $errors['password']; ?></span> <?php endif; ?>

        <label>Xác nhận mật khẩu</label>
        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
        <?php if(isset($errors['confirm_password'])): ?> <span class="error-msg"><?php echo $errors['confirm_password']; ?></span> <?php endif; ?>
        <button type="submit" class="btn">Đăng Ký</button>
    </form>

    <div class="links">
        Đã có tài khoản? <a href="login.php">Đăng Nhập</a><br>
        <a href="reset-password.php">Quên mật khẩu?</a>
    </div>
</div>

</body>
</html>