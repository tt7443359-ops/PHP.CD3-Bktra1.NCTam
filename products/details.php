<?php
session_start();
require_once 'db_product.php';

// Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn lấy sản phẩm
$sql = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: index1.php");
    exit();
}
?>

<?php
$page_title = $product['name'] . " - Chi tiết";
require_once "../include/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/details.css">


    <div class="detail-container">
        <div class="left">
            <a href="index1.php" class="btn-back">← Quay lại danh sách</a>
            <img src="../img.products/<?php echo $product['image_id']; ?>" alt="cover">
        </div>
        
        <div class="right">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</div>
            
            <h3>Cốt truyện:</h3>
            <p class="desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <div class="buy-actions">
                <?php if (!isset($_SESSION['admin_logged_in'])): ?>
                    <?php if (isset($_SESSION['user']) || isset($_COOKIE['stored_email'])): ?>
                        <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn-buy" style="background: #64c5c5;">Thêm giỏ hàng</a>
                        <a href="checkout.php?id=<?php echo $product['id']; ?>" class="btn-buy">Mua Ngay</a>
                    <?php else: ?>
                        <a href="../login.php" class="btn-buy" style="background: #64c5c5;">Thêm giỏ hàng</a>
                        <a href="../login.php" class="btn-buy">Mua Ngay</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>