<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . $base_url . "login");
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id <= 0) {
    header("Location: " . $base_url . "order-history?cancel_err=4");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['user']);
$u_res = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
$u_row = mysqli_fetch_assoc($u_res);
if (!$u_row) {
    header("Location: " . $base_url . "order-history?cancel_err=2");
    exit();
}
$uid = $u_row['id'];

// Kiểm tra tính hợp lệ của đơn
$o_res = mysqli_query($conn, "SELECT id, status, order_date, total_price FROM orders WHERE id=$order_id AND user_id=$uid LIMIT 1");
$order = mysqli_fetch_assoc($o_res);
if (!$order || !in_array($order['status'], ['chờ xác nhận', 'đã xác nhận'])) {
    header("Location: " . $base_url . "order-history?cancel_err=3");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reason = trim($_POST['reason'] ?? '');
    $custom_reason = trim($_POST['custom_reason'] ?? '');

    if (empty($reason)) {
        $errors['reason'] = "Vui lòng chọn lý do hủy đơn!";
    } elseif ($reason === 'Lý do khác' && empty($custom_reason)) {
        $errors['custom_reason'] = "Vui lòng nhập lý do cụ thể!";
    }

    if (empty($errors)) {
        $final_reason = ($reason === 'Lý do khác') ? $custom_reason : $reason;
        $final_reason_db = mysqli_real_escape_string($conn, $final_reason);

        // Đảm bảo cột tồn tại
        try {
            mysqli_query($conn, "ALTER TABLE orders ADD COLUMN cancel_reason TEXT NULL");
        } catch (Exception $e) {
        }
        try {
            mysqli_query($conn, "ALTER TABLE orders ADD COLUMN cancel_request TINYINT(1) NOT NULL DEFAULT 0");
        } catch (Exception $e) {
        }

        $sql_update = "UPDATE orders SET cancel_request=1, cancel_reason='$final_reason_db' WHERE id=$order_id";
        if (mysqli_query($conn, $sql_update)) {
            echo "<script>alert('Gửi yêu cầu hủy đơn thành công!'); window.location.href='" . $base_url . "order-history';</script>";
            exit();
        } else {
            $errors['db'] = "Có lỗi xảy ra khi cập nhật CSDL.";
        }
    }
}

// Lấy sản phẩm của đơn đó
$items_res = mysqli_query($conn, "SELECT p.name, od.quantity, od.price_at_buy 
                                  FROM order_details od 
                                  JOIN products p ON od.product_id = p.id 
                                  WHERE od.order_id = $order_id");
$items = $items_res ? mysqli_fetch_all($items_res, MYSQLI_ASSOC) : [];

$page_title = "Yêu cầu hủy đơn hàng";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/order_history.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/request_cancel.css">

<div class="cancel-page-container">
    <h2>Yêu Cầu Hủy Đơn Hàng</h2>

    <div class="order-info">
        <p><b>Mã đơn:</b> #<?php echo $order['id']; ?></p>
        <p><b>Ngày đặt:</b> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
        <p><b>Tổng giá trị:</b> <?php echo number_format($order['total_price'], 0, ',', '.'); ?>đ</p>

        <p style="margin-top:10px;"><b>Sản phẩm trong đơn:</b></p>
        <ul class="order-items-list">
            <?php foreach ($items as $item): ?>
                <li>
                    <?php echo htmlspecialchars($item['name']); ?>
                    <b>x <?php echo $item['quantity']; ?></b>
                    — <?php echo number_format($item['price_at_buy'], 0, ',', '.'); ?>đ
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if (isset($errors['db'])): ?>
        <div style="color:red; margin-bottom:15px;"><?php echo $errors['db']; ?></div><?php endif; ?>

    <form method="POST" action="" novalidate>
        <?php echo csrf_tag(); ?>
        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

        <div class="cancel-form-group">
            <label>Vui lòng chọn lý do hủy đơn của bạn:</label>
            <select name="reason" id="reasonSel" onchange="toggleCustom(this)" required>
                <option value="">— Chọn lý do —</option>
                <option value="Thay đổi chi tiết SP" <?php echo (isset($_POST['reason']) && $_POST['reason'] == 'Thay đổi chi tiết SP') ? 'selected' : ''; ?>>Thay đổi chi tiết SP (SLL, loại...)</option>
                <option value="Thay đổi màu sắc" <?php echo (isset($_POST['reason']) && $_POST['reason'] == 'Thay đổi màu sắc') ? 'selected' : ''; ?>>Thay đổi màu sắc</option>
                <option value="Thay đổi kích thước" <?php echo (isset($_POST['reason']) && $_POST['reason'] == 'Thay đổi kích thước') ? 'selected' : ''; ?>>Thay đổi kích thước</option>
                <option value="Tìm được nơi mua rẻ hơn" <?php echo (isset($_POST['reason']) && $_POST['reason'] == 'Tìm được nơi mua rẻ hơn') ? 'selected' : ''; ?>>Tìm được nơi mua rẻ hơn</option>
                <option value="Muốn đổi địa chỉ giao hàng" <?php echo (isset($_POST['reason']) && $_POST['reason'] == 'Muốn đổi địa chỉ giao hàng') ? 'selected' : ''; ?>>Muốn đổi địa chỉ giao hàng
                </option>
                <option value="Lý do khác" <?php echo (isset($_POST['reason']) && $_POST['reason'] == 'Lý do khác') ? 'selected' : ''; ?>>Lý do khác...</option>
            </select>
            <?php if (isset($errors['reason'])): ?><span
                    style="color:red; font-size:13px; display:block; margin-top:5px;"><?php echo $errors['reason']; ?></span><?php endif; ?>
        </div>

        <div class="cancel-form-group" id="customWrap"
            style="<?php echo (isset($_POST['reason']) && $_POST['reason'] == 'Lý do khác') ? 'display:block;' : 'display:none;'; ?>">
            <label>Nhập lý do cụ thể:</label>
            <textarea name="custom_reason" rows="4"
                placeholder="Vui lòng ghi lý do"><?php echo htmlspecialchars($_POST['custom_reason'] ?? ''); ?></textarea>
            <?php if (isset($errors['custom_reason'])): ?><span
                    style="color:red; font-size:13px; display:block; margin-top:5px;"><?php echo $errors['custom_reason']; ?></span><?php endif; ?>
        </div>

        <div class="cancel-btn-group">
            <a href="<?php echo $base_url; ?>order-history" class="btn-back">Bỏ</a>
            <button type="submit" class="btn-submit-cancel">Hủy</button>
        </div>
    </form>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/order.js"></script>

</body>

</html>