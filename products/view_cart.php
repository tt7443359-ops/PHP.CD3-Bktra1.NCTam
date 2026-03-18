<?php
session_start();
require_once 'db_product.php';
require_once("../auth_check.php");

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<div style='text-align:center; padding:50px; font-family:Poppins;'>
            <h2>Giỏ hàng trống</h2>
            <a href='index1.php' style='color:#007bff; text-decoration:none;'>ủng hộ Admin...</a>
          </div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }
        
        .cart-container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .cart-table th {
            background-color: #f8f9fa;
            color: #7f8c8d;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }
        
        .cart-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .qty {
            color: #e67e22;
            font-weight: bold;
        }
        
        .subtotal {
            font-weight: 600;
        }
        
        .text-right {
            text-align: right;
            font-size: 18px;
            color: #7f8c8d;
        }
        
        .grand-total {
            font-size: 22px;
            color: #e74c3c;
            font-weight: 700;
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-back {
            text-decoration: none;
            color: #3498db;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-back:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        .btn-checkout {
            background: linear-gradient(75deg, #7551d8, #64c5c5 , #1992d8);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);
            transition: 0.3s transform;
        }
        
        .btn-checkout:hover {
            background: linear-gradient(75deg, #7551d8, #64c5c5);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(230, 126, 34, 0.4);
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h2>Giỏ hàng</h2>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tổng cộng</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grand_total = 0;
            foreach ($_SESSION['cart'] as $id => $qty): 
                $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
                $p = mysqli_fetch_assoc($res);
                $subtotal = $p['price'] * $qty;
                $grand_total += $subtotal;
            ?>
            <tr>
                <td class="product-name"><?php echo $p['name']; ?></td>
                <td><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</td>
                <td class="qty"><?php echo $qty; ?></td>
                <td class="subtotal"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">Thành tiền:</td>
                <td class="grand-total"><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</td>
            </tr>
        </tfoot>
    </table>

    <div class="cart-actions">
        <a href="index1.php" class="btn-back">Tiếp tục mua sắm</a>
        <a href="order_success.php" class="btn-checkout">Tiến hành thanh toán</a>
    </div>
</div>

</body>
</html>