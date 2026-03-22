<?php 
require_once("auth_check.php");
require_once("products/db_product.php");

// Hàm đếm nhanh dữ liệu từ một bảng
function countRows($conn, $table, $column = "*") {
    $sql = ($column === "*") ? "SELECT COUNT(*) as total FROM $table" : "SELECT COUNT(DISTINCT $column) as total FROM $table";
    $result = mysqli_query($conn, $sql);
    return ($result) ? mysqli_fetch_assoc($result)['total'] : 0;
}

// Lấy thông số từ CSDL
$total_products = countRows($conn, "products");
$total_users    = countRows($conn, "users", "email"); 
$total_orders   = countRows($conn, "contacts");

$page_title = "Dashboard Admin";
require_once "include/header.php";
?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="dashboard.css">
<style>
    body {
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden;
    }
    .main {
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                          url('img/inori-yuzuriha-crying-guilty-crown-thumb.jpg') !important;
        background-size: cover !important;      
        background-position: center !important;
        background-attachment: fixed !important;
        margin: 0 !important; 
        padding: 60px 20px !important;
        width: 100% !important; 
        min-height: 85vh !important;
        border-radius: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    /* Footer */
    footer {
      text-align: center;
      padding: 20px;
      background: linear-gradient(75deg, #ec1a3d, #bb2d58, #e28ac0);
      color: #ffffff;
    }
</style>

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
        <p><?= $total_orders ?></p></center>
      </div>
    </div>

    <button class="btn" onclick="window.location.href='products/admin_products.php';" style="margin-top: 20px; padding: 10px 20px; background: #ec1a3d; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Quản Lý Sản Phẩm
    </button>
  </div>

  <footer>© 2026 - Bản quyền thuộc về (NCTâm)</footer>

</body>
</html>