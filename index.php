<?php
session_start();
$page_title = "Trang Chủ";
require_once "include/header.php";
?>
<style>
/* HERO */
.hero {
    height: 90vh;
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
    padding: 10px;
    margin-top: 10px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
}
</style>

<section class="hero">
    <div>
        <h1>Chào Mừng Đến Với ShopLIGHTNOVEL2X</h1>
        <p>
            Hãy đăng ký hoặc đăng <br>nhập để trải nghiệm ngay hôm nay!
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