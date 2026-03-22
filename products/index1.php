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
<style>
  * {
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


    .btn-add, .btn-edit01 {
      display: inline-flex;  
      align-items: center;
      justify-content: center;
      height: 45px !important;  
      padding: 0 15px !important; 
      font-size: 14px !important;
      font-weight: 600;
      border-radius: 8px;       
      text-decoration: none;
      transition: 0.3s;
      box-sizing: border-box;   
    }
    
    .btn-add {
      background: linear-gradient(75deg, #7551d8, #64c5c5, #1992d8);
      color: white;
      border: none;
      box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }
    
    .btn-add:hover {
      background: linear-gradient(75deg, #7551d8, #64c5c5);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }

    .btn-edit01 {
      color: #64c5c5;        
      border: 1px solid #2f9c9c;
      background: white;
    }
    
    .btn-edit01:hover {
      background: #d1f8ef;     
      color: #4f46e5;
      border-color: #4f46e5;
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
      
    .product-name {
      font-size: 15px; font-weight: 600; color: #333;
      margin: 12px 0 5px; line-height: 1.4;
      height: 42px; 
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
  }
      
    .product-actions{
      display:flex;
      justify-content:center;
      gap:10px;
    }
</style>
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