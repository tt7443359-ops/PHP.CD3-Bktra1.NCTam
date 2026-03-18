<?php
session_start();
require_once 'db_product.php';
require_once("../auth_check.php");

// Nếu không có giỏ hàng và cũng không có ID đặt hàng.
if ((!isset($_SESSION['cart']) || empty($_SESSION['cart'])) && !isset($_GET['id'])) {
    header("Location: index1.php");
    exit();
}

//Hoàn tất quy trình mua sắm
unset($_SESSION['cart']); 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .order-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 450px;
            border-top: 5px solid #2ecc71; 
        }
        .check-icon {
            width: 70px;
            height: 70px;
            background: #e8faf0;
            color: #2ecc71;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            margin: 0 auto 20px;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            color: #7f8c8d;
            font-size: 15px;
            margin-bottom: 25px;
        }
        .btn-return {
            background-color: #2c3e50;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            transition: 0.3s;
        }
        .btn-return:hover {
            background-color: #34495e;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="order-box">
    <div class="check-icon">✓</div>
    <h1>Hoàn tất đặt hàng!</h1>
    <p>Hệ thống đã ghi nhận đơn hàng<br>Cảm ơn quý khách đã tin tưởng lựa chọn Shop Light Novel của <strong>NCTâm</strong>.</p>
    <a href="index1.php" class="btn-return">Quay lại cửa hàng</a>
</div>

</body>
</html>