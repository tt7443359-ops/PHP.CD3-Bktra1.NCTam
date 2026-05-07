<?php 
require_once __DIR__ . "/../includes/auth_check.php";
require_once __DIR__ . "/../includes/db_product.php";
restrictToAdmin();

// Hàm đếm nhanh dữ liệu từ một bảng
function countRows($conn, $table, $column = "*") {
    $sql = ($column === "*") ? "SELECT COUNT(*) as total FROM $table" : "SELECT COUNT(DISTINCT $column) as total FROM $table";
    $result = mysqli_query($conn, $sql);
    return ($result) ? mysqli_fetch_assoc($result)['total'] : 0;
}

// Lấy thông số từ CSDL
$total_products = countRows($conn, "products");
$total_users    = countRows($conn, "users", "email"); 
$total_orders   = countRows($conn, "orders");
$total_messages = countRows($conn, "contacts");

$page_title = "Dashboard";
require_once __DIR__ . "/../includes/header.php";
?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/dashboard.css">

  <div class="main">
    <h1>Tổng Quan Hệ Thống</h1>

    <div class="cards">
      <div class="card">
        <center><h3>Sản Phẩm</h3>
        <p><?= $total_products ?></p></center> 
      </div>

      <div class="card">
        <center><h3>Khách Đã Vào</h3>
        <p><?= $total_users ?></p> </center>
      </div>

      <div class="card">
        <center><h3>Phản Hồi</h3>
        <p><?= $total_messages ?></p></center>
      </div>
    </div>
    
    <div style="display: flex; gap: 15px; margin-top: 20px;">
        <button class="btn" onclick="window.location.href='<?php echo $base_url; ?>admin/products';" style="padding: 10px 20px; background: #ec1a3d; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Quản Lý Sản Phẩm
        </button>
        <button class="btn" onclick="window.location.href='<?php echo $base_url; ?>admin/orders';" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Quản Lý Đơn Hàng
        </button>
    </div>
  </div>

  <footer>© 2026 - Bản quyền thuộc về (NCTâm)</footer>

</body>
</html>