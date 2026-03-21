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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="logo.css">
  <link rel="stylesheet" href="dashboard.css">
</head>
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
    /* Navbar */
    .navbar {
      background: linear-gradient(75deg, #ec1a3d, #bb2d58, #e28ac0);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
      border-bottom: 1px solid #eee !important;
      padding: 10px 60px; 
      display: flex;
      justify-content: space-between; 
      align-items: center; 
    }
    .nav-links a {
      text-decoration: none;
      color: #fffdfd !important; 
      font-weight: 600 !important;
      margin-left: 20px;
      transition: 0.3s;
    }
    .nav-links a:hover {
      color: #4f46e5 !important;;
    }
    .navbar h2 {
      font-weight: 700;
    }
    /* Footer */
    footer {
      text-align: center;
      padding: 20px;
      background: linear-gradient(75deg, #ec1a3d, #bb2d58, #e28ac0);
      color: #ffffff;
    }
    
   
</style>
<body>

  <div class="navbar">
    <h2><div class="logo-container"><img src="img/images.jpg" alt="Logo"></div></h2>
    <div class="nav-links">
      <a href="admin_messages.php">Phản Hồi Khách Hàng</a>
      <a href="index.php">Trang Chủ</a>
      <a href="products/admin_products.php">Sản phẩm</a>
      <a href="contact.php">Liên Hệ</a>
      <a href="logout.php">Đăng Xuất</a>
    </div>
  </div>

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

    <button class="btn" onclick="window.location.href='products/admin_products.php';">
        Quản Lý Sản Phẩm
    </button>
  </div>

  <footer>© 2026 - Bản quyền thuộc về (NCTâm)</footer>

</body>
</html>