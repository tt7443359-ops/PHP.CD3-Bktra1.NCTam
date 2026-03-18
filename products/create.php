<?php
session_start();
require_once '../db.php';
require_once("../auth_check.php");
//Check Bug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $price = mysqli_real_escape_string($conn, $_POST["price"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $image = mysqli_real_escape_string($conn, $_POST["image"]); 

    $sql = "INSERT INTO products (id, name, price, description, image_id) 
        VALUES ('$id', '$name', '$price', '$description', '$image')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index1.php"); 
        exit();
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm Mới - ShopLIGHTNOVEL2X</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(to bottom, #f0fcf9, #e6f0ff); min-height: 100vh; }
        .navbar { background: #fff; padding: 15px 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .nav-links a { text-decoration: none; margin-left: 25px; color: #333; font-weight: 600; transition: 0.3s; }
        .nav-links a:hover { color: #4f46e5; }
        .main-content { display: flex; justify-content: center; padding: 40px 20px; }
        .form-card { background: #fff; width: 100%; max-width: 500px; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); text-align: center; }
        h2 { color: #1f2937; margin-bottom: 25px; }
        .form-group { text-align: left; margin-bottom: 15px; }
        label { display: block; font-weight: 600; margin-bottom: 5px; color: #4b5563; font-size: 14px; }
        input, textarea { width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 10px; background: #f9fafb; outline: none; transition: 0.3s; }
        input:focus, textarea:focus { border-color: #4f46e5; background: #fff; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .btn-submit { width: 100%; background: linear-gradient(75deg, #7551d8, #64c5c5, #1992d8); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { opacity: 0.9; transform: translateY(-1px); }
        .back-link { display: inline-block; margin-top: 20px; color: #4f46e5; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo-text">Shop Light Novel</div>
        <div class="nav-links">
            <a href="../dashboard.php">Dashboard</a>
            <a href="index1.php">Sản phẩm</a>
            <a href="../logout.php">Đăng Xuất</a>
        </div>
    </div>
    <div class="main-content">
        <div class="form-card">
            <h2>Thêm Sản Phẩm Mới</h2>
            <form method="POST">
                <div class="form-group">
                    <label>ID Sản Phẩm (Số thứ tự)</label>
                    <input type="number" name="id" placeholder="Nhập ID" required>
                </div>
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
                    <input type="text" name="image" placeholder="...jpg" required>
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