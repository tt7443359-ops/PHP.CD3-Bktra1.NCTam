<?php
session_start();
require_once '../db.php';
require_once("../auth_check.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.php"); // Admin
    exit();
}

//Check Bug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    header("Location: index1.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Lấy dữ liệu cũ để hiện lên form
$res = mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'");
$product = mysqli_fetch_assoc($res);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $price = str_replace('.', '', $_POST["price"]); // Xử lý dấu chấm giá tiền
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $image = mysqli_real_escape_string($conn, $_POST["image"]);

    $sql = "UPDATE products SET name='$name', price='$price', description='$description', image_id='$image' WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_products.php");
        exit();
    }
}
?>

<?php
$page_title = "Cập Nhật Sản Phẩm - ShopMANGA2X";
require_once "../include/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: linear-gradient(to bottom, #f0fcf9, #e6f0ff); min-height: 100vh; }
    .main-content { display: flex; justify-content: center; padding: 40px 20px; }
    .form-card { background: #fff; width: 100%; max-width: 500px; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); text-align: center; }
    h2 { color: #1f2937; margin-bottom: 25px; }
    .form-group { text-align: left; margin-bottom: 15px; }
    label { display: block; font-weight: 600; margin-bottom: 5px; color: #4b5563; font-size: 14px; }
    input, textarea { width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 10px; background: #f9fafb; outline: none; }
    .btn-submit { width: 100%; background: linear-gradient(75deg, #7551d8, #64c5c5, #1992d8); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 600; cursor: pointer; margin-top: 10px; }
    .back-link { display: inline-block; margin-top: 20px; color: #4f46e5; text-decoration: none; font-size: 14px; }
</style>

    <div class="main-content">
        <div class="form-card">
            <h2>Chỉnh Sửa Thông Tin</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Tên Sản Phẩm</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Giá (VNĐ)</label>
                    <input type="text" name="price" value="<?php echo number_format($product['price'], 0, '', '.'); ?>" 
                           oninput="this.value = this.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');" required>
                </div>
               <div class="form-group">
                   <label>Tên tệp ảnh hiện tại</label>
                   <input type="text" name="image" value="<?php echo isset($product['image_id']) ? $product['image_id'] : ''; ?>" required>
               </div>
                <div class="form-group">
                    <label>Mô Tả</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                <button type="submit" class="btn-submit">Lưu Thay Đổi</button>
            </form>
            <a href="index1.php" class="back-link">Hủy bỏ thay đổi</a>
        </div>
    </div>
</body>
</html>