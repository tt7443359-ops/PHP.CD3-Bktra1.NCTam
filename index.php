<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Chủ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="Stylesheet" href="logo.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f7fa;
        }

        /* HEADER */
        header {
            background: linear-gradient(75deg, #d52b1e, #7551d8, #64c5c5);
            padding: 20px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav a {
            margin-left: 30px;
            text-decoration: none;
            color: #e7e2e2;
            font-weight: 500;
        }

        nav a:hover {
            color: #182230;
        }

        /* HERO */
        .hero {
            height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: linear-gradient(to bottom,#f0fcf9, #e6f0ff);
            padding: 20px;

            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/anime-guilty-crown-inori-yuzuriha-hd-wallpaper-preview.jpg');
            background-size: cover;      
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; 
            
            color: white;
        
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            color: #e9e6e6;
            margin-bottom: 30px;
        }

        .btn {
            padding: 16px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(75deg, #d52b1e, #7551d8, #64c5c5);
            color: white;
        }

        .btn-primary:hover {
             background: linear-gradient(75deg, #d52b1e, #7551d8);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        /* FOOTER */
        footer {
            background: linear-gradient(75deg, #d52b1e, #7551d8, #64c5c5);
            text-align: center;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<header>
    <div class="logo-container"> <img src="img/images.jpg" alt="Logo"></div>
    <nav>
        <a href="index.php">Trang Chủ</a>
        <?php 
        // Hiện nếu là Khách hoặc Admin
        if (isset($_SESSION['user']) || isset($_SESSION['admin_logged_in'])): ?>
            <a href="products/index1.php">Sản phẩm</a>
        <?php endif; ?>
        <a href="contact.php">Liên hệ</a>
    </nav>
</header>

<section class="hero">
    <div>
        <h1>Chào Mừng Đến Với ShopLIGHTNOVEL2X</h1>
        <p>
            Đây là nơi bạn có thể khám phá các dịch vụ tuyệt vời của Shop.
            Hãy đăng ký hoặc đăng <br>nhập để trải nghiệm ngay hôm nay!<br>"Và đơn nhiên là d**l có dịch vụ nào ở đây cả"
            <br>Surprise mother f*cker
        </p>

        <?php if (isset($_SESSION['admin_logged_in'])): ?>
            <p style="font-weight: bold; color: #d64d76;">
                HI ADMIN! 
            </p>
        
        <?php elseif (isset($_SESSION['user']) || isset($_COOKIE['stored_email'])): ?>
            <p style="font-style: italic; color: #c4d4d4;">
                NHÌN C** VÀO MUA SẢN PHẨM ĐỂ ỦNG HỘ ADMIN
            </p>
        
        <?php else: ?>
            <a href="register.php" class="btn btn-primary">Đăng Ký</a>
            <a href="login.php" class="btn btn-secondary">Đăng Nhập</a>
        <?php endif; ?>
    </div>
</section>

<footer>
    © <?php echo date("Y"); ?> - Bản quyền thuộc về (NCTâm)
</footer>

</body>
</html>
<script>
    // Xóa dấu vết trước, (còn lỗi load lại, lùi vẫn được D*m)
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = function () {
        window.history.go(1);
    };
</script>