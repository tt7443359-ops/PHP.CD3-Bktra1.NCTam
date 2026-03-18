<?php
session_start();
require_once 'db_product.php';
require_once("../auth_check.php");

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

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['name']; ?> - Chi tiết</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0fcf9; padding: 50px; }
        .detail-container { 
            max-width: 900px; margin: 0 auto; background: white; 
            padding: 30px; border-radius: 20px; display: flex; gap: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .left img { width: 350px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .right { flex: 1; }
        .price { color: #ef4444; font-size: 24px; font-weight: bold; margin: 20px 0; }
        .desc { color: #555; line-height: 1.6; margin-bottom: 30px; }
        .btn-back { text-decoration: none; color: #4f46e5; font-weight: 600; display: inline-block; margin-bottom: 20px; }
        .buy-actions { display: flex; gap: 15px; }

        .btn-buy { 
            background: linear-gradient(75deg, #4f46e5, #7c3aed); color: white; 
            padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: bold;
            transition: 0.3s;
        }
        .btn-buy:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(124, 58, 237, 0.4); }
    </style>
</head>
<body>

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
                <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn-buy" style="background: #64c5c5;">Thêm giỏ hàng</a>
                <a href="checkout.php?id=<?php echo $product['id']; ?>" class="btn-buy">Mua Ngay</a>
            </div>
        </div>
    </div>

</body>
</html>