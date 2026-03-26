<?php
session_start();
require_once 'db_product.php'; 

$products = getAllProducts($conn);

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = intval($_GET['id']);
    $sql = "DELETE FROM products WHERE id = $id_to_delete";
    mysqli_query($conn, $sql);
    
    header("Location: index1.php");
    exit();
}
?>

<?php
$page_title = "Sản Phẩm";
require_once "../include/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/index1.css">
  <div class="container">
    <div class="header-action">
    <h2>Danh Sách Sản Phẩm</h2>
    <div style="width: 150px;"></div> 
    </div>

    <div class="product-grid">
    <?php 
    if (!empty($products)):
        foreach ($products as $row): 
    ?>
    <div class="product-card">
        <div class="product-img">
            <?php 
                //Tự động chèn ảnh theo id ảnh trong CSDL
                $imgName = !empty($row['image_id']) ? $row['image_id'] : "default-manga.jpg";
                $imgPath = "../img.products/" . $imgName; 
            ?>
          <a href="details.php?id=<?php echo $row['id']; ?>">
            <img src="<?php echo $imgPath; ?>" alt="product">
          </a>
        </div>
        <div class="product-name">
          <a href="details.php?id=<?php echo $row['id']; ?>">
          <?php echo htmlspecialchars($row['name']); ?></div>
          </a>
        <div class="product-price" style="color: #ef4444; font-weight: bold; margin-bottom: 10px;">
            <?php echo number_format((float)$row['price'], 0, ',', '.'); ?> VNĐ
        </div>
        <div class="product-actions">
         <?php // Hiện nếu Admin
             if (isset($_SESSION['admin_logged_in'])): ?>
         <?php // Nếu là Khách đã đăng nhập (Check cả Session hoặc Cookie)
             elseif (isset($_SESSION['user']) || isset($_COOKIE['stored_email'])): ?>
             <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn-edit01" style="border-color: #64c5c5; color: #64c5c5;">
                 Thêm giỏ hàng
             </a>
             <a href="order_success.php?action=buy_now&id=<?php echo $row['id']; ?>" class="btn-add">
                 Đặt hàng
             </a>
         
         <?php else: ?>
             <a href="../login.php" class="btn-edit01" style="border-color: #64c5c5; color: #64c5c5;">
                 Thêm giỏ hàng
             </a>
             <a href="../login.php" class="btn-add">
                 Đặt hàng
             </a>
         <?php endif; ?>
     </div>
         </div>
         <?php 
             endforeach; 
         else:
         ?>
             <p style="grid-column: span 4; text-align: center;">Chưa có sản phẩm nào.</p>
         <?php endif; ?>
       </div>
  </div>

  <footer>
    &copy; 2026 ShopLIGHTNOVEL2X
  </footer>

</body>
</html>