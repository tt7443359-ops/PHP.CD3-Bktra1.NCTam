<?php
    session_start();
    require_once 'db.php';
    
    $errors = [];
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'] ?? '';

        // KIỂM TRA ADMIN
        $email_esc = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT * FROM `admin` WHERE (username = '$email_esc' OR email = '$email_esc') AND password = '$password' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $row['username'];
            
            header("Location: dashboard.php"); 
            exit();
        }
        //Khách
        // Lấy dữ liệu từ Cookie đã lưu ở trang Register
        $cookie_email = $_COOKIE['stored_email'] ?? '';
        $cookie_pass = $_COOKIE['stored_password'] ?? '';

        // Ktra đăng nhập
        if (empty($email) || empty($password)) {
            $errors['login'] = "Vui lòng nhập đầy đủ thông tin.";
        } elseif ($email === $cookie_email && $password === $cookie_pass) {
            $_SESSION["user"] = $email;  

            // Chặn ghi nếu là admin
            global $admin_email; 
            if ($email !== $admin_email) {
                $ip = $_SERVER['REMOTE_ADDR']; 
        
                // Chống lặp trong 1 giờ
                $check_sql = "SELECT id FROM login_history 
                              WHERE email = '$email' 
                              AND login_time > NOW() - INTERVAL 1 HOUR";
                $check_res = mysqli_query($conn, $check_sql);
        
                if (mysqli_num_rows($check_res) == 0) {
                    $log_sql = "INSERT INTO login_history (email, ip_address) VALUES ('$email', '$ip')";
                    mysqli_query($conn, $log_sql); 
                }
            }
             
            // khớp -> Chuyển 
            header("Location: products/index1.php");
            exit();
        } else {
            // Không khớp
            $errors['login'] = "Email hoặc mật khẩu không chính xác.";
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