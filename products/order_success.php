<?php
    session_start();
    require_once 'db_product.php'; 
    require_once '../auth_check.php';
    
    $product_list = "";
    $grand_total = 0;
    
    // Mua ngay
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $res = mysqli_query($conn, "SELECT id, price FROM products WHERE id = $id");
        $p = mysqli_fetch_assoc($res);
        if ($p) {
            $product_list = $p['id']; // Lấy ID
            $grand_total = $p['price'];
        }
    } 
    
    // Giỏ hàng
    if (empty($product_list)) {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: index1.php");
            exit();
        }
    
        $ids = array();
        foreach ($_SESSION['cart'] as $id => $qty) {
            $res = mysqli_query($conn, "SELECT id, price FROM products WHERE id = $id");
            $p = mysqli_fetch_assoc($res);
            if ($p) {
                $ids[] = $p['id']; // Gom ID
                $grand_total += ($p['price'] * $qty);
            }
        }
        $product_list = implode(", ", $ids);
    }
    
    $buyer_name = $_SESSION['user']['fullname'] ?? $_COOKIE['stored_username'] ?? "Khách ẩn danh";
    $buyer_email = $_SESSION['user']['email'] ?? $_COOKIE['stored_email'] ?? "Không rõ";

    $sql_order = "INSERT INTO orders (fullname, email, product_id, total_price) 
                  VALUES ('$buyer_name', '$buyer_email', '$product_list', '$grand_total')";
    
    if (mysqli_query($conn, $sql_order)) {
        if (isset($_SESSION['cart'])) unset($_SESSION['cart']); 
    } else {
        die("Lỗi CSDL: " . mysqli_error($conn));
    }
?>
<?php
$page_title = "Xác nhận đơn hàng";
require_once "../include/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/order_success.css">

<div class="order-box">
    <div class="check-icon">✓</div>
    <h1>Hoàn tất đặt hàng!</h1>
    <p>Chào <b><?php echo htmlspecialchars($buyer_name); ?></b>,<br>Đơn hàng đã ghi nhận ID sản phẩm: <?php echo htmlspecialchars($product_list); ?></p>
    <a href="index1.php" class="btn-return">Quay lại cửa hàng</a>
</div>
</body>
</html>