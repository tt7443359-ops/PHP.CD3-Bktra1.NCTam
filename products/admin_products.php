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

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản Lý Sản Phẩm - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="Stylesheet" href="../logo.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; text-decoration: none; }
    body { background: linear-gradient(to bottom, #f0fcf9, #e6f0ff); min-height: 100vh; display: flex; flex-direction: column; }
    .navbar { background: #f5f5f5; padding: 25px 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .logo-container { border: 2px solid #4f46e5; }
    .nav-links a { margin-left: 25px; color: #333; font-weight: 600; transition: 0.3s; }
    .nav-links a:hover { color: #4f46e5; }
    .container { padding: 40px 60px; flex: 1; }
    .header-action { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    h2 { color: #1f2937; font-size: 28px; }
    /*Css nút thêm products*/ 
    .btn-add {
      display: inline-flex; align-items: center; justify-content: center;
      height: 45px; padding: 0 20px; font-size: 14px; font-weight: 600;
      border-radius: 8px; color: white; border: none;
      background: linear-gradient(75deg, #7551d8, #64c5c5, #1992d8);
      box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3); transition: 0.3s;
    }
    .btn-add:hover {
      background: linear-gradient(75deg, #7551d8, #64c5c5);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr); /* Tối đa 4 khung mỗi hàng */
      gap: 15px;
    }

    /* Card hàng ngang thu hẹp*/
    .product-card {
      background: white;
      display: flex; 
      align-items: center;
      padding: 10px 15px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: 0.3s;
    }
    .product-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }

    .product-img img {
      width: 60px;
      height: 80px;
      object-fit: cover;
      border-radius: 6px;
      margin-right: 15px;
      background: #f9f9f9;
    }

    .product-info {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-width: 0; /*...tên dài */
    }

    .product-name {
      font-size: 14px; font-weight: 600; color: #333;
      margin-bottom: 5px;
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 1;
    }

    .product-price {
      color: #ef4444; font-weight: bold; font-size: 13px;
    }

    .product-actions {
      display: flex;
      gap: 8px;
      margin-left: 10px;
    }

    /*Css nút sửa xóa */
    .btn-edit, .btn-delete {
      font-size: 12px; font-weight: 600; padding: 5px 10px;
      border-radius: 6px; transition: 0.2s; border: 1px solid;
    }
    .btn-edit { color: #4f46e5; border-color: #4f46e5; }
    .btn-edit:hover { background: #4f46e5; color: white; }
    .btn-delete { color: #ef4444; border-color: #ef4444; }
    .btn-delete:hover { background: #ef4444; color: white; }

    footer { text-align: center; padding: 20px; background: #f5f5f5; color: #555; margin-top: auto; }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="logo-container"><img src="../img/images.jpg" alt="Logo" style="height: 50px;"></div>
    <div class="nav-links">
      <a href="../dashboard.php">Dashboard</a>
      <a href="index1.php">Giao diện User</a>
      <a href="../logout.php">Đăng xuất</a>
    </div>
  </div>

  <div class="container">
    <div class="header-action">
      <h2>Quản Lý Sản Phẩm (Admin)</h2>
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
    &copy; 2026 ShopLIGHTNOVEL2X - Quản lý Admin.
  </footer>

</body>
</html>