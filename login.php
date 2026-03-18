<?php
    session_start();
    require_once 'db.php'; 
    
    $errors = [];
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $errors['login'] = "Vui lòng nhập đầy đủ thông tin.";
        } else {
            // 1. Kiểm tra Admin
            $sql_admin = "SELECT * FROM `admin` WHERE (username = '$email' OR email = '$email') AND password = '$password' LIMIT 1";
            $res_admin = mysqli_query($conn, $sql_admin);

            if (mysqli_num_rows($res_admin) > 0) {
                $row = mysqli_fetch_assoc($res_admin);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = $row['username'];
                header("Location: dashboard.php"); 
                exit();
            }

            // 2. Kiểm tra khách (Truy vấn users)
            $sql_user = "SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1";
            $res_user = mysqli_query($conn, $sql_user);

            if (mysqli_num_rows($res_user) > 0) {
               $user_data = mysqli_fetch_assoc($res_user);
               $_SESSION["user"] = $user_data['email'];
               $_SESSION["username"] = $user_data['fullname'];
            
               // Hồi sinh cookie
               setcookie("stored_email", $user_data['email'], time() + 86400, "/");
               setcookie("stored_password", $user_data['password'], time() + 86400, "/");
            
               header("Location: products/index1.php");
               exit();
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

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(to bottom,#f0fcf9, #e6f0ff);
}

.container{
    background:#fff;
    width:550px;
    padding:50px;
    border-radius:20px;
    box-shadow:0 25px 60px rgba(0,0,0,0.15);
}

h2{
    text-align:center;
    margin-bottom:35px;
    font-size:32px;
}

label{
    display:block;
    margin-top:20px;
    margin-bottom:8px;
    font-weight:600;
    font-size:16px;
}

input{
    width:100%;
    padding:16px;
    border-radius:12px;
    border:1px solid #ddd;
    font-size:16px;
    background: #f3f4f6;
}

input:focus{
    outline:none;
    border-color:#4f46e5;
}

.btn{
    width:100%;
    margin-top:30px;
    padding:18px;
    border:none;
    border-radius:14px;
    background: linear-gradient(75deg, #7551d8, #64c5c5 , #1992d8);
    color:white;
    font-size:18px;
    font-weight:bold;
    cursor:pointer;
}

.btn:hover{
    background: linear-gradient(75deg, #7551d8, #64c5c5);
}

.links{
    text-align:center;
    margin-top:25px;
    font-size:16px;
}

.links a{
    color:#4f46e5;
    text-decoration:none;
    font-weight:600;
}

.links a:hover{
    text-decoration:underline;
}

</style>
</head>
<body>

<div class="container">
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