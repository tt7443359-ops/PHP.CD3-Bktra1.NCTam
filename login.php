<?php
    session_start();
    require_once 'db.php'; 
    
    $errors = [];
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';

        if (empty($email) || empty($password)) {
            $errors['login'] = "Vui lòng nhập đầy đủ thông tin.";
        } else {
            // 1. Kiểm tra Admin
            $sql_admin = "SELECT * FROM `admin` WHERE (username = '$email' OR email = '$email') LIMIT 1";
            $res_admin = mysqli_query($conn, $sql_admin);

            if (mysqli_num_rows($res_admin) > 0) {
                $row = mysqli_fetch_assoc($res_admin);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user'] = $row['username'];
                    header("Location: dashboard.php"); 
                    exit();
                }
            }

            // 2. Kiểm tra khách (Truy vấn users)
            $sql_user = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
            $res_user = mysqli_query($conn, $sql_user);

            if (mysqli_num_rows($res_user) > 0) {
               $user_data = mysqli_fetch_assoc($res_user);
               if (password_verify($password, $user_data['password'])) {
                   $_SESSION["user"] = $user_data['email'];
                   $_SESSION["username"] = $user_data['fullname'];
                
                   // Hồi sinh cookie
                   setcookie("stored_email", $user_data['email'], time() + 86400, "/");
                   setcookie("stored_password", $password, time() + 86400, "/");
                
                   header("Location: products/index1.php");
                   exit();
               } else {
                   $errors['login'] = "Email hoặc mật khẩu không chính xác.";
               }
            } else {
                $errors['login'] = "Email hoặc mật khẩu không chính xác.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng Nhập</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="container fade-in-down">
    <h2>Đăng Nhập</h2>
      <!-- Login Form -->
        <?php if(isset($errors['login'])): ?>
            <p style="color: red; font-size: 0.8em;"><?php echo $errors['login']; ?></p>
        <?php endif; ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" placeholder="Nhập email" required>

        <label>Mật khẩu</label>
        <input type="password" name="password" placeholder="Nhập mật khẩu" required>

        <button type="submit" class="btn">Đăng Nhập</button>
    </form>

    <div class="links">
        Chưa có tài khoản? <a href="register.php">Đăng Ký</a><br>
        <a href="reset-password.php">Quên mật khẩu?</a>
    </div>
</div>

</body>
</html>