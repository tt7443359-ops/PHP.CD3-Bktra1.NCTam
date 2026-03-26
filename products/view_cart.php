<?php
session_start();
require_once 'db_product.php';
require_once("../auth_check.php");

// Phân quyền dùng chung: Admin hoặc Khách
if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user']) && !isset($_COOKIE['stored_email'])) {
    header("Location: ../login.php");
    exit();
}
    // Bỏ chọn sản phẩm
    if (isset($_POST['delete_selected']) && isset($_POST['remove_ids'])) {
        foreach ($_POST['remove_ids'] as $id_to_remove) {
            unset($_SESSION['cart'][$id_to_remove]);
        }
        header("Location: view_cart.php"); 
        exit();
    }

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<div style='text-align:center; padding:50px; font-family:Poppins;'>
            <h2>Giỏ hàng trống</h2>
            <a href='index1.php' style='color:#007bff; text-decoration:none;'>ủng hộ Admin...</a>
          </div>";
    exit();
}
?>

<?php
$page_title = "Giỏ Hàng Của Bạn";
require_once "../include/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/view_cart.css">


<div class="cart-container">
    <h2>Giỏ hàng</h2>

    <form method="POST" action="view_cart.php">

    <table class="cart-table">
        <thead>
            <tr>
                <th>Chọn</th> <th>Sản phẩm</th>
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
                <td class="check-col">
                    <input type="checkbox" name="remove_ids[]" value="<?php echo $id; ?>">
                </td>
                <td class="product-name"><?php echo $p['name']; ?></td>
                <td><?php echo number_format($p['price'], 0, ',', '.'); ?>đ</td>
                <td class="qty"><?php echo $qty; ?></td>
                <td class="subtotal"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <button type="submit" name="delete_selected" class="btn-remove" 
                            onclick="return confirm('Bỏ?')">
                            Bỏ Mục Sản Phẩm
                    </button>
                </td>
                <td colspan="2" class="text-right">Thành tiền:</td>
                <td class="grand-total"><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</td>
            </tr>
        </tfoot>
    </table>

    </form> <div class="cart-actions">
        <a href="index1.php" class="btn-back">Tiếp tục mua sắm</a>
        <a href="order_success.php" class="btn-checkout">Tiến hành thanh toán</a>
    </div>
</div>

</body>
</html>