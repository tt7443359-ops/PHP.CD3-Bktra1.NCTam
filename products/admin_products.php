<?php
session_start();
require_once 'db_product.php'; 
require_once '../auth_check.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index1.php");
    exit();
}

$products = getAllProducts($conn);

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = intval($_GET['id']);
    $sql = "DELETE FROM products WHERE id = $id_to_delete";
    mysqli_query($conn, $sql);
    header("Location: admin_products.php");
    exit();
}
?>

<?php
$page_title = "Quản Lý Sản Phẩm";
require_once "../include/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/admin_products.css">
  <div class="container">
    <div class="header-action">
      <h2>Quản Lý Sản Phẩm</h2>
      <a href="create.php" class="btn-add">+ Thêm Sản Phẩm Mới</a>
    </div>

    <div class="product-grid">
    <?php if (!empty($products)): foreach ($products as $row): ?>
        <div class="product-card">
            <a href="details.php?id=<?php echo $row['id']; ?>" class="product-img">
                <?php $imgPath = "../img.products/" . (!empty($row['image_id']) ? $row['image_id'] : "default-manga.jpg"); ?>
                <img src="<?php echo $imgPath; ?>" alt="product">
            </a>
            
            <div class="product-info">
                <a href="details.php?id=<?php echo $row['id']; ?>" class="product-name">
                    <?php echo htmlspecialchars($row['name']); ?>
                </a>
                <div class="product-price"><?php echo number_format((float)$row['price'], 0, ',', '.'); ?> VNĐ</div>
            </div>

            <div class="product-actions">
                <a href="update.php?id=<?php echo $row['id']; ?>" class="btn-edit">Sửa</a>
                <a href="admin_products.php?action=delete&id=<?php echo $row['id']; ?>" 
                   onclick="return confirm('Gay?')" class="btn-delete">Xóa</a>
            </div>
        </div>
    <?php endforeach; else: ?>
        <p style="text-align: center; grid-column: span 3;">Chưa có sản phẩm nào.</p>
    <?php endif; ?>
    </div>
  </div>

  <footer>
    &copy; 2026 ShopLIGHTNOVEL2X - Quản lý.
  </footer>

</body>
</html>