<?php
session_start();
require_once '../db.php';
require_once("../auth_check.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $price = mysqli_real_escape_string($conn, $_POST["price"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    
    // 2. Xử lý tệp ảnh
    $image_name = $_FILES['image']['name']; // Lấy tên file ảnh
    $target = "../img.products/" . basename($image_name);

    // Di chuyển ảnh vào thư mục img.products
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO products (name, price, description, image_id) 
                VALUES ('$name', '$price', '$description', '$image_name')";

        if (mysqli_query($conn, $sql)) {
            header("Location: admin_products.php "); 
            exit();
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
    } else {
        echo "Lỗi: Không thể tải ảnh lên thư mục img.products!";
    }
}
?>

<?php
$page_title = "Thêm Sản Phẩm Mới";
require_once "../include/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/create.css">

    <div class="main-content">
        <div class="form-card">
            <h2>Thêm Sản Phẩm Mới</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Tên Sản Phẩm</label>
                    <input type="text" name="name" placeholder="Nhập tên sản phẩm" required>
                </div>
                <div class="form-group">
                    <label>Giá (VNĐ)</label>
                    <input type="number" name="price" placeholder="Nhập giá" required>
                </div>
                <div class="form-group">
                    <label>Tên tệp ảnh (Kèm đuôi .jpg, .png)</label>
                    <input type="file" name="image" placeholder="...jpg" required>
                </div>
                <div class="form-group">
                    <label>Mô Tả</label>
                    <textarea name="description" rows="3" placeholder="Nhập mô tả sản phẩm"></textarea>
                </div>
                <button type="submit" class="btn-submit">Thêm Sản Phẩm</button>
            </form>
            <a href="index1.php" class="back-link">Quay lại danh sách</a>
        </div>
    </div>
</body>
</html>