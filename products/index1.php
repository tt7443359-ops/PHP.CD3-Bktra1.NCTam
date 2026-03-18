<?php
session_start();
require_once 'db_product.php'; 
require_once '../auth_check.php';

$products = getAllProducts($conn);

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = intval($_GET['id']);
    $sql = "DELETE FROM products WHERE id = $id_to_delete";
    mysqli_query($conn, $sql);
    
    header("Location: index1.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản Lý Sản Phẩm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="Stylesheet" href="../logo.css">
</head>
<style>
  * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
      text-decoration: none;
    }

    body {
      background: linear-gradient(to bottom, #f0fcf9, #e6f0ff);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar {
      background: #f5f5f5;
      padding: 15px 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .logo-container {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid #4f46e5;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .nav-links a {
      text-decoration: none;
      margin-left: 25px;
      color: #333;
      font-weight: 600;
      transition: 0.3s;
    }

    .nav-links a:hover {
      color: #4f46e5;
    }

    .container {
      padding: 40px 60px;
      flex: 1;
    }

    .header-action {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    h2 {
      color: #1f2937;
      font-size: 28px;
    }

    .btn-add {
      background: linear-gradient(75deg, #7551d8, #64c5c5 , #1992d8);
      color: white;
      text-decoration: none;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: 600;
      transition: 0.3s;
      box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }

    .btn-add:hover {
      background: linear-gradient(75deg, #7551d8, #64c5c5);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }

    .btn-edit {
      color: #4f46e5;
      text-decoration: none;
      font-weight: 600;
      padding: 5px 12px;
      border: 1px solid #4f46e5;
      border-radius: 6px;
      transition: 0.2s;
    }

    .btn-edit:hover {
      background: #4f46e5;
      color: white;
    }

    .btn-delete {
      color: #ef4444;
      text-decoration: none;
      font-weight: 600;
      padding: 5px 12px;
      border: 1px solid #ef4444;
      border-radius: 6px;
      transition: 0.2s;
    }

    .btn-delete:hover {
      background: #ef4444;
      color: white;
    }

    footer {
      text-align: center;
      padding: 20px;
      background: #f5f5f5;
      color: #555;
    }

    .product-grid{
      display:grid;
      grid-template-columns:repeat(4,1fr);
      gap:25px;
    }
      
    .product-card{
      background:white;
      padding:20px;
      border-radius:15px;
      box-shadow:0 10px 25px rgba(0,0,0,0.08);
      text-align:center;
      transition:0.3s;
    }
      
    .product-card:hover{
      transform:translateY(-5px);
    }
      
    .product-img img{
      width: 100%;
      height: 250px;
      object-fit: contain;
      background-color: #f9f9f9;
      border-radius: 10px;
      margin-bottom: 10px;
    }
      
    .product-name{
      font-weight:600;
      font-size:16px;
      margin-bottom:10px;
    }
      
    .product-actions{
      display:flex;
      justify-content:center;
      gap:10px;
    }
</style>
<body>

  <div class="navbar">
    <div class="logo-container">
      <img src="../img/images.jpg" alt="Logo">
    </div>
    <div class="nav-links">
      <?php 
        $total_items = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $qty) {
                $total_items += $qty;
            }
        }
        ?>
        <a href="view_cart.php" class="btn-cart" style="position: relative; margin-right: 15px; text-decoration: none;">
            🛒 Giỏ hàng 
            <?php if ($total_items > 0): ?>
                <span style="background: red; color: white; border-radius: 50%; padding: 2px 7px; font-size: 11px; position: absolute; top: -10px; right: -15px; font-weight: bold;">
                    <?php echo $total_items; ?>
                </span>
            <?php endif; ?>
        </a>
      <?php 
      // Hiện nếu Admin
      if (isset($_SESSION['admin_logged_in'])): 
        ?>
      <a href="../dashboard.php">Dashboard</a>
      <?php endif; ?>
      <a href="../products/index1.php">Sản phẩm</a>
      <a href="../contact.php">Liên Hệ</a>
      <a href="../logout.php">Đăng xuất</a>
    </div>
  </div>

  <div class="container">
    <div class="header-action">
      <h2>Danh Sách Sản Phẩm</h2>
      <?php
      // Hiện nếu Admin
      if (isset($_SESSION['admin_logged_in'])): 
        ?>
      <a href="create.php" class="btn-add">+ Thêm Sản Phẩm Mới</a>
      <?php endif; ?>
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
             <a href="update.php?id=<?php echo $row['id']; ?>" class="btn-edit">Sửa</a>
             <a href="index1.php?action=delete&id=<?php echo $row['id']; ?>" 
                onclick="return confirm('D**m Tk Gay')" class="btn-delete">Xóa</a>
     
         <?php //Nếu là khách
             elseif (isset($_COOKIE['stored_email'])): ?>
             <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn-edit" style="border-color: #64c5c5; color: #64c5c5;">
                 Thêm giỏ hàng
             </a>
             <a href="order_success.php?id=<?php echo $row['id']; ?>" class="btn-add" style="padding: 5px 12px; font-size: 13px; display: inline-flex; align-items: center;">
                 Đặt hàng
             </a>
     
         <?php else: ?>
             <a href="../login.php" style="font-size: 12px; color: #999; text-decoration: none;">
                 Đăng nhập để mua hàng
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
    &copy; 2026 ShopLIGHTNOVEL2X - Hệ thống quản lý kho.
  </footer>

</body>
</html>