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
        $check_mail = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
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
        // Lưu vào bảng users
        $sql = "INSERT INTO users (fullname, email, password, ip_address) VALUES ('$username', '$email', '$password', '$ip')";
        
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

   <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
     background: linear-gradient(to bottom,#f0fcf9, #e6f0ff);
}

.container {
    background: #ffffff;
    width: 550px;              
    padding: 50px;             
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.15);
}

h2 {
    text-align: center;
    margin-bottom: 35px;
    font-size: 32px;          
}

label {
    display: block;
    margin-top: 18px;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 16px;
}

input {
    width: 100%;
    padding: 16px;             
    border-radius: 12px;
    border: 1px solid #ddd;
    font-size: 16px;
    background: #f3f4f6;
}

input:focus {
    outline: none;
    border-color: #4f46e5;
}

.btn {
    width: 100%;
    margin-top: 30px;
    padding: 18px;            
    border: none;
    border-radius: 14px;
   background: linear-gradient(75deg, #7551d8, #64c5c5 , #1992d8);
    color: white;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
}

.btn:hover {
    background: linear-gradient(75deg, #7551d8, #64c5c5);
}

.links {
    text-align: center;
    margin-top: 25px;
    font-size: 16px;
}

.links a {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 600;
}
</style>
</head>
<body>
      
<div class="container">
    <h2>Đăng Ký</h2>
     <?php if ($success_msg): ?>
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