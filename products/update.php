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
$page_title = "Cập Nhật Sản Phẩm";
require_once "../include/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/update.css">

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
                   <input type="file" name="image" value="<?php echo isset($product['image_id']) ? $product['image_id'] : ''; ?>" required>
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