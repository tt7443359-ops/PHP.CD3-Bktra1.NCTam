<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Demo 
    echo "<script>alert('Yêu cầu đặt lại mật khẩu đã được gửi!');</script>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt Lại Mật Khẩu</title>
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
    margin-bottom:25px;
}

input:focus{
    outline:none;
    border-color:#4f46e5;
}

.btn{
    width:100%;
    padding:18px;
    border:none;
    border-radius:14px;
    background:#4f46e5;
    color:white;
    font-size:18px;
    font-weight:bold;
    cursor:pointer;
}

.btn:hover{
    background:#4338ca;
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
    <h2>Đặt Lại Mật Khẩu</h2>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" placeholder="Nhập email của bạn" required>

        <button type="submit" class="btn">Gửi Yêu Cầu</button>
    </form>

    <div class="links">
        <a href="login.php">Quay lại đăng nhập</a>
    </div>
</div>

</body>
</html>