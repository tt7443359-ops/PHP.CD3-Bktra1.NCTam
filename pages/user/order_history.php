<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . $base_url . "login");
    exit();
}

// Đảm bảo cột cancel tồn tại
try {
    mysqli_query($conn, "ALTER TABLE orders ADD COLUMN cancel_reason TEXT NULL");
} catch (Exception $e) {
}
try {
    mysqli_query($conn, "ALTER TABLE orders ADD COLUMN cancel_request TINYINT(1) NOT NULL DEFAULT 0");
} catch (Exception $e) {
}

$email = mysqli_real_escape_string($conn, $_SESSION['user'] ?? '');
$c_res = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
$user_id = 0;
if ($c_row = mysqli_fetch_assoc($c_res)) {
    $user_id = $c_row['id'];
}

// Fetch avatar info for sidebar
$info_res = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$customer = mysqli_fetch_assoc($info_res);
$has_custom_avatar = !empty($customer['avatar']) && $customer['avatar'] !== 'default_avatar.png' && file_exists(__DIR__ . "/../../public/uploads/avatars/" . $customer['avatar']);
$avatar_src = $has_custom_avatar 
    ? $base_url . "public/uploads/avatars/" . $customer['avatar']
    : $base_url . "public/assets/img/default_avatar.png";

$status_filter = "";
$status_val = "";
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $s = mysqli_real_escape_string($conn, $_GET['status']);
    $status_val = $s;
    if ($s === 'đang giao') {
        $status_filter = " AND o.status IN ('đang giao', 'đã xác nhận')";
    } else {
        $status_filter = " AND o.status = '$s'";
    }
}

$sql = "SELECT o.* FROM orders o
           WHERE o.user_id = $user_id $status_filter
           ORDER BY o.order_date DESC";
$result = mysqli_query($conn, $sql);
$orders = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$order_items = [];
foreach ($orders as $o) {
    $oid = $o['id'];
    $det = mysqli_query($conn, "SELECT p.id AS product_id, p.name, p.image_id, od.quantity, od.price_at_buy
                                FROM order_details od
                                JOIN products p ON od.product_id = p.id
                                WHERE od.order_id = $oid");

    $order_items[$oid] = $det ? mysqli_fetch_all($det, MYSQLI_ASSOC) : [];
}
?>

<?php
$page_title = __('order_history_title');
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/profile.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/order_history.css">

<div class="profile-layout-store">
    <div class="profile-sidebar-store">
        <div class="user-intro-store">
            <img src="<?php echo $avatar_src; ?>" alt="Avatar" class="avatar-mini-store">
            <div class="user-intro-text-store">
                <div class="user-name-store"><?php echo htmlspecialchars($customer['fullname'] ? $customer['fullname'] : $customer['email']); ?></div>
                <a href="<?php echo $base_url; ?>profile" style="text-decoration:none;"><div class="edit-profile-store"><i class="fa-solid fa-pen"></i> <?php echo __('profile'); ?></div></a>
            </div>
        </div>
    </div>
    
    <div class="profile-main-store" style="background: transparent; box-shadow: none; padding: 0;">
        <div class="order-tabs-store">
            <a href="?status=" class="<?php echo empty($status_val) ? 'active' : ''; ?>"><?php echo __('status_all'); ?></a>
            <a href="?status=chờ xác nhận" class="<?php echo $status_val === 'chờ xác nhận' ? 'active' : ''; ?>"><?php echo __('status_pending'); ?></a>
            <a href="?status=đang giao" class="<?php echo $status_val === 'đang giao' ? 'active' : ''; ?>"><?php echo __('status_shipping'); ?></a>
            <a href="?status=đã giao" class="<?php echo $status_val === 'đã giao' ? 'active' : ''; ?>"><?php echo __('status_completed'); ?></a>
            <a href="?status=huỷ" class="<?php echo $status_val === 'huỷ' ? 'active' : ''; ?>"><?php echo __('status_cancelled'); ?></a>
        </div>

        <?php if (isset($_GET['cancel_ok'])): ?>
            <div class="msg-success" style="background: #fff; margin-bottom: 15px;"><?php echo __('cancel_request_sent'); ?></div>
        <?php elseif (isset($_GET['cancel_err'])): ?>
            <div class="msg-error" style="background: #fff; margin-bottom: 15px;"><?php echo __('cancel_request_failed'); ?></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="store-empty-order">
                <p><?php echo __('no_orders'); ?></p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $o): 
                $oid = $o['id'];
                $items = $order_items[$oid] ?? [];
                $already_req = ($o['cancel_request'] ?? 0) == 1;
                $st = $o['status'];
            ?>
                <div class="store-order-card">
                    <div class="store-order-header">
                        <div class="store-shop-name">Novel2x Shop <a href="<?php echo $base_url; ?>shop" class="btn-view-shop"><i class="fa-solid fa-store"></i> <?php echo __('view_shop'); ?></a></div>
                        <div class="store-order-status">
                            <?php 
                            if ($st == 'huỷ') echo '<span style="color:#ee4d2d;">' . __('order_cancelled_label') . '</span>';
                            elseif ($st == 'đã giao') echo '<span style="color:#26aa99;"><i class="fa-solid fa-truck"></i> ' . __('shipping_success') . '</span> | <span style="color:#ee4d2d;">' . mb_strtoupper(__('status_completed'), 'UTF-8') . '</span>';
                            else echo '<span style="color:#ee4d2d;">' . mb_strtoupper(__($st), 'UTF-8') . '</span>';
                            ?>
                        </div>
                    </div>
                    
                    <?php foreach ($items as $di): 
                        $img = !empty($di['image_id']) ? $base_url . 'public/assets/products_img/' . htmlspecialchars($di['image_id']) : $base_url . 'public/assets/images/default-product.png';
                    ?>
                        <div class="store-order-item">
                            <div class="store-item-img" style="cursor: pointer;" onclick="window.location.href='<?php echo $base_url; ?>shop/details/<?php echo $di['product_id'] ?? 0; ?>'">
                                <img src="<?php echo $img; ?>" alt="">
                            </div>
                            <div class="store-item-info">
                                <div class="store-item-name" style="cursor: pointer;" onclick="window.location.href='<?php echo $base_url; ?>shop/details/<?php echo $di['product_id'] ?? 0; ?>'"><?php echo htmlspecialchars($di['name']); ?></div>
                                <div class="store-item-qty">x<?php echo $di['quantity']; ?></div>
                            </div>
                            <div class="store-item-price" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center;">
                                <div style="color: #ee4d2d; margin-bottom: 8px;">₫<?php echo number_format($di['price_at_buy'], 0, ',', '.'); ?></div>
                                <?php if (in_array($st, ['đã giao', 'huỷ'])): ?>
                                    <a href="<?php echo $base_url; ?>shop/details/<?php echo $di['product_id'] ?? 0; ?>" class="btn-store-solid" style="padding: 4px 12px; font-size: 12px; white-space: nowrap; border-radius: 2px;"><?php echo __('buy_again'); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="store-order-footer">
                        <div class="store-order-total">
                            <?php echo __('subtotal'); ?>: <span class="total-price">₫<?php echo number_format($o['total_price'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="store-order-actions">
                            <?php if (in_array($st, ['chờ xác nhận', 'đã xác nhận']) && !$already_req): ?>
                                <a href="<?php echo $base_url; ?>request-cancel/<?php echo $oid; ?>" class="btn-store-outline"><?php echo __('request_cancel'); ?></a>
                            <?php endif; ?>
                            <!-- Các trạng thái đang giao, đã giao, hủy không hiển thị nút ở đây nữa -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
</body>

</html>