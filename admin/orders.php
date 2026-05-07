<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
restrictToAdmin();


$action_msg = '';
$action_type = 'success';


// ── Cập nhật
if (isset($_POST['update_status'])) {
    $oid = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $res = mysqli_query($conn, "SELECT status FROM orders WHERE id=$oid");
    $old_status = $res ? mysqli_fetch_assoc($res)['status'] : '';

    if ($new_status === 'huỷ' && $old_status !== 'huỷ') {

        $chk_res = mysqli_query($conn, "SELECT cancel_reason FROM orders WHERE id=$oid");
        $cancel_reason = ($chk_res && $chk = mysqli_fetch_assoc($chk_res)) ? mysqli_real_escape_string($conn, $chk['cancel_reason']) : 'Admin chủ động hủy thủ công';

        $details = mysqli_query($conn, "SELECT od.product_id, od.quantity, od.price_at_buy, p.name FROM order_details od JOIN products p ON od.product_id=p.id WHERE od.order_id=$oid");
        if ($details) {
            while ($d = mysqli_fetch_assoc($details)) {
                $pid = $d['product_id'];
                $qty = $d['quantity'];
                $price = $d['price_at_buy'];
                $pname = mysqli_real_escape_string($conn, $d['name']);
                mysqli_query($conn, "UPDATE products SET stock_quantity = stock_quantity + $qty WHERE id = $pid");
            }
        }
    }
    mysqli_query($conn, "UPDATE orders SET status='$new_status', cancel_request=0 WHERE id=$oid");
    header("Location: " . $base_url . "admin/orders?updated=1");
    exit();
}

    // ── Lấy đơn có yêu cầu hủy (hiển thị riêng)
    $sql_cancel = "SELECT o.*, u.fullname AS registered_name, u.phone, u.address
                   FROM orders o
                   LEFT JOIN users u ON o.user_id = u.id
                   WHERE o.cancel_request = 1
                   ORDER BY o.order_date DESC";
    $cancel_requests = mysqli_fetch_all(mysqli_query($conn, $sql_cancel), MYSQLI_ASSOC);
    
    // ── Tất cả đơn hàng
    $sql = "SELECT o.*, u.fullname AS registered_name, u.phone, u.address
               FROM orders o
               LEFT JOIN users u ON o.user_id = u.id
               ORDER BY o.order_date DESC";
    $result = mysqli_query($conn, $sql);
    $orders = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>
    
<?php
$page_title = "Quản Lý Đơn Hàng";
require_once __DIR__ . "/../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/admin_shared.css?v=<?php echo time(); ?>">

<div class="admin-wrapper">
<div class="admin-container">
    <div class="admin-header-action">
        <h2>Quản Lý Đơn Hàng</h2>
        <div class="action-buttons">
            <a href="<?php echo $base_url; ?>admin/products" class="btn-secondary">Quản lý SP</a>
        </div>
    </div>

    <?php if ($action_msg): ?>
        <div class="ao-msg ao-msg-<?php echo $action_type; ?>"><?php echo $action_msg; ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
    <?php endif; ?>

    <!-- ═══ YÊU CẦU HỦY ĐƠN ═══ -->
    <?php if (!empty($cancel_requests)): ?>
        <div class="cancel-requests-badge" onclick="window.location.href='<?php echo $base_url; ?>admin/cancel-requests';" style="cursor: pointer; border-radius: 50%; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: #e53935; color: #fff; font-weight: 700; box-shadow: 0 2px 10px rgba(229, 57, 53, 0.4); margin-bottom: 15px; font-size: 13px; border: 2px solid #fff;">
            <?php echo count($cancel_requests); ?>
        </div>
    <?php endif; ?>

    <!-- ═══ TẤT CẢ ĐƠN HÀNG ═══ -->
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 60px;">STT</th>
                    <th>Khách Hàng</th>
                    <th>Ngày Đặt</th>
                    <th>Sản Phẩm (SL × Giá)</th>
                    <th>Lời Nhắn</th>
                    <th>Tổng Cộng</th>
                    <th style="text-align: center;">Trạng Thái</th>
                    <th style="text-align: center;">Tình Trạng</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1;
                foreach ($orders as $o): ?>
                    <tr>
                        <td>
                            <strong><?php echo $stt++; ?></strong><br>
                            <small style="color:#888;">(#<?php echo $o['id']; ?>)</small>

                        </td>
                        <td style="text-align:left;">
                            <b><?php echo htmlspecialchars($o['registered_name'] ?: $o['fullname_guest']); ?></b><br>
                            <small><?php echo htmlspecialchars($o['email_guest'] ?? ''); ?></small><br>
                            <small>SĐT: <?php echo htmlspecialchars($o['shipping_phone'] ?: ($o['phone'] ?: 'Chưa có')); ?></small><br>
                            <small>Đ/C: <?php echo htmlspecialchars($o['shipping_address'] ?: ($o['address'] ?: 'Chưa có')); ?></small>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($o['order_date'])); ?></td>
                        <td style="text-align:left; cursor: pointer;" onclick="openOrderDetailsModal(this.querySelector('.prods-list').innerHTML)">
                            <ul class="prods-list">
                                <?php
                                $oid = $o['id'];
                                $det = mysqli_query($conn, "SELECT p.name, od.quantity, od.price_at_buy FROM order_details od JOIN products p ON od.product_id=p.id WHERE od.order_id=$oid");
                                if ($det && mysqli_num_rows($det) > 0) {
                                    while ($d = mysqli_fetch_assoc($det)) {
                                        $style = ($o['status'] === 'huỷ') ? 'style="color:#9ca3af; text-decoration:line-through; font-size:12px;"' : '';
                                        echo '<li ' . $style . '>' . htmlspecialchars($d['name']) . ' - <b>x' . $d['quantity'] . '</b> (' . number_format($d['price_at_buy'], 0, ',', '.') . 'đ)</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </td>
                        <td style="max-width: 150px;">
                            <?php if (!empty($o['note'])): ?>
                                <div style="font-size: 13px; color: #05a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer;" onclick="openNoteModal('<?php echo htmlspecialchars(addslashes($o['note'])); ?>')">
                                    <?php echo htmlspecialchars($o['note']); ?>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 13px; color: #aaa;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><b style="color:#e53935;"><?php echo number_format((float) $o['total_price'], 0, ',', '.'); ?>đ</b>
                        </td>
                        <td style="text-align: center;">
                            <form method="POST" style="display:flex;gap:5px;justify-content:center;align-items:center;">
                                <?php echo csrf_tag(); ?>
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" class="status-select">
                                    <?php
                                    $statuses = ['chờ xác nhận', 'đã xác nhận', 'đang giao', 'đã giao', 'huỷ'];
                                    foreach ($statuses as $st) {
                                        $sel = ($o['status'] === $st) ? 'selected' : '';
                                        echo "<option value='$st' $sel>" . ucfirst($st) . "</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" name="update_status" class="btn-update">Cập nhật</button>
                            </form>
                            <?php if (!empty($o['cancel_reason']) && ($o['cancel_request'] ?? 0) == 0 && $o['status'] == 'huỷ'): ?>
                                <small style="color:#6b7280;font-size:11px; display:block; margin-top:5px;">Lý do:
                                    <?php echo htmlspecialchars($o['cancel_reason']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($o['status'] === 'chờ xác nhận'): ?>
                                <span style="color:#fca311;font-weight:bold;">Đang chờ</span>
                            <?php elseif ($o['status'] === 'huỷ'): ?>
                                <span style="color:#9ca3af;font-weight:bold;">Đã hoàn kho</span>
                            <?php else: ?>
                                <span style="color:green;font-weight:bold;">Đang xử lý</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Modal xem lời nhắn -->
<div id="noteModal" class="store-modal">
    <div class="store-modal-content" style="width: 400px;">
        <div class="modal-header" style="padding: 15px 20px; font-size: 16px; font-weight: 500; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <span>Lời Nhắn</span>
            <span style="cursor:pointer; font-size: 20px; color: #999;" onclick="closeNoteModal()">&times;</span>
        </div>
        <div class="modal-body" style="padding: 20px; font-size: 14px; color: #333; line-height: 1.5; white-space: pre-wrap; word-wrap: break-word;" id="noteModalContent">
        </div>
        <div class="modal-footer" style="padding: 15px 20px; text-align: right; border-top: 1px solid #eee; background: #fafafa;">
            <button type="button" style="padding: 8px 16px; border: none; background: #ee4d2d; color: #fff; cursor: pointer; border-radius: 2px;" onclick="closeNoteModal()">Đóng</button>
        </div>
    </div>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/order.js?v=<?php echo time(); ?>"></script>

<footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> ShopLIGHTNOVEL2X - Quản lý.
</footer>

</body>
</html>