<?php
session_start();
$page_title = "Trang Chủ";
require_once "include/header.php";
?>
<link rel="stylesheet" href="css/index.css">

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