<?php
require_once __DIR__ . '/../../includes/db_product.php'; 
require_once __DIR__ . '/../../includes/auth_check.php';
restrictToAdmin();

$products = getAllProducts($conn);
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = intval($_GET['id']);
    $sql = "DELETE FROM products WHERE id = $id_to_delete";
    mysqli_query($conn, $sql);
    header("Location: " . $base_url . "admin/products");
    exit();
}
?>

<?php
$page_title = "Quản Lý Sản Phẩm";
require_once __DIR__ . '/../../includes/header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/admin_shared.css">
  
<div class="admin-wrapper">
<div class="admin-container">
    
    <div class="admin-header-action">
      <h2>Quản Lý Sản Phẩm</h2>
      <div class="action-buttons">
         <a href="<?php echo $base_url; ?>admin/categories" class="btn-primary" style="margin-right: 10px;">Quản Lý Danh Mục</a>
         <a href="<?php echo $base_url; ?>admin/create-product" class="btn-add">Thêm Sản Phẩm</a>
      </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th style="text-align: center; width: 150px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($products)): foreach ($products as $row): ?>
                <tr>
                    <td>
                        <a href="<?php echo $base_url; ?>shop/details/<?php echo $row['id']; ?>" class="product-img">
                            <?php $imgPath = $base_url . "public/assets/products_img/" . (!empty($row['image_id']) ? htmlspecialchars($row['image_id']) : "default-manga.jpg"); ?>
                            <img src="<?php echo $imgPath; ?>" alt="product" style="width: 60px; height: 80px; object-fit: cover; border-radius: 6px; background: #f9f9f9;">
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo $base_url; ?>shop/details/<?php echo $row['id']; ?>" class="product-name" style="color: #333; font-weight: 600; display: block; margin-bottom: 5px;" title="<?php echo htmlspecialchars($row['category_name'].': '.$row['name']); ?>">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </a>
                        <div style="font-size: 11px; color: #777;">
                            Kho: <b><?php echo $row['stock_quantity']; ?></b> 
                            <span style="color: <?php echo $row['status'] == 'hiện' ? '#4CAF50' : '#f44336'; ?>; font-weight: bold; margin-left: 5px;">
                                [<?php echo $row['status']; ?>]
                            </span>
                        </div>
                    </td>
                    <td style="color: #4b5563; font-weight: 500;">
                        <?php echo htmlspecialchars($row['category_name']); ?>
                    </td>
                    <td style="color: #ef4444; font-weight: bold; font-size: 14px;">
                        <?php echo number_format((float)$row['price'], 0, ',', '.'); ?> VNĐ
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="<?php echo $base_url; ?>admin/update-product/<?php echo $row['id']; ?>" class="btn-edit">Sửa</a>
                            <a href="<?php echo $base_url; ?>admin/products?action=delete&id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Xác nhận xóa sản phẩm này?')" class="btn-delete">Xóa</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #666; font-weight: 500; padding: 30px;">Chưa có sản phẩm nào.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
  </div>
</div>

  <footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> ShopLIGHTNOVEL2X - Quản lý.
  </footer>

</body>
</html>