<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
restrictToAdmin();

$msg = "";
$msg_type = "";



// Xử lý Duyệt / Từ chối
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['order_id'])) {
    
    $action = $_POST['action'];
    $order_id = intval($_POST['order_id']);
    
    // Kiểm tra đơn hàng đang xin hủy
    $chk_res = mysqli_query($conn, "SELECT id, cancel_reason FROM orders WHERE id = $order_id AND cancel_request = 1 LIMIT 1");
    if ($chk_order = mysqli_fetch_assoc($chk_res)) {
        $cancel_reason = mysqli_real_escape_string($conn, $chk_order['cancel_reason']);
        
        if ($action === 'approve') {
            mysqli_begin_transaction($conn);
            try {
                // Lấy chi tiết đơn
                $det_res = mysqli_query($conn, "SELECT od.product_id, od.quantity, od.price_at_buy, p.name 
                                                FROM order_details od 
                                                JOIN products p ON od.product_id = p.id 
                                                WHERE od.order_id = $order_id");
                
                while ($item = mysqli_fetch_assoc($det_res)) {
                    $pid = $item['product_id'];
                    $qty = $item['quantity'];
                    $price = $item['price_at_buy'];
                    $pname = mysqli_real_escape_string($conn, $item['name']);
                    
                    // 1. Cộng lại số lượng kho
                    mysqli_query($conn, "UPDATE products SET stock_quantity = stock_quantity + $qty WHERE id = $pid");
                }
                
                // 2. Cập nhật status bảng orders thành 'huỷ', tắt cờ cancel_request
                mysqli_query($conn, "UPDATE orders SET status = 'huỷ', cancel_request = 0 WHERE id = $order_id");
                
                mysqli_commit($conn);
                header("Location: " . $base_url . "admin/cancel_requests.php?msg_type=success&msg=" . urlencode("Đã duyệt và dọn dẹp đơn #$order_id thành công!"));
                exit();
                
            } catch (Exception $e) {
                mysqli_rollback($conn);
                header("Location: " . $base_url . "admin/cancel_requests.php?msg_type=error&msg=" . urlencode("Lỗi kỹ thuật: " . $e->getMessage()));
                exit();
            }
            
        } elseif ($action === 'reject') {
            // Từ chối hủy: Xóa cờ xin hủy và xóa lý do
            mysqli_query($conn, "UPDATE orders SET cancel_request = 0, cancel_reason = NULL WHERE id = $order_id");
            header("Location: " . $base_url . "admin/cancel_requests.php?msg_type=info&msg=" . urlencode("Đã TỪ CHỐI yêu cầu hủy đơn #$order_id."));
            exit();
        }
    } else {
        header("Location: " . $base_url . "admin/cancel_requests.php?msg_type=error&msg=" . urlencode("Yêu cầu không hợp lệ hoặc đơn hàng không còn ở trạng thái xin hủy."));
        exit();
    }
}

// Xử lý thông báo từ URL
if (isset($_GET['msg'], $_GET['msg_type'])) {
    $msg = $_GET['msg'];
    $msg_type = $_GET['msg_type'];
}

// Lấy danh sách đang chờ duyệt
$sql = "SELECT o.*, 
        (SELECT u.fullname FROM users u WHERE u.id = o.user_id) as registered_name,
        (SELECT GROUP_CONCAT(CONCAT(p.name, ' (x', od.quantity, ')') SEPARATOR '<br>') 
         FROM order_details od JOIN products p ON od.product_id = p.id 
         WHERE od.order_id = o.id) as items_summary
        FROM orders o 
        WHERE o.cancel_request = 1 
        ORDER BY o.order_date DESC";
$requests_result = mysqli_query($conn, $sql);
$requests = $requests_result ? mysqli_fetch_all($requests_result, MYSQLI_ASSOC) : [];

$page_title = "Quản lý Yêu Cầu Hủy Đơn";
require_once __DIR__ . "/../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/admin_shared.css">

<div class="requests-wrap">
    <h2>Yêu Cầu Hủy Đơn</h2>
    


    <div class="table-wrapper">
        <table class="req-table">
            <thead>
                <tr>
                    <th>Mã Đơn / Ngày</th>
                    <th>Nguồn Gốc (Khách)</th>
                    <th>Sản Phẩm Đang Đặt</th>
                    <th>Lý Do Hủy</th>
                    <th>Quyết Định</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($requests)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color: #9ca3af;">
                        Không có yêu cầu hủy đơn nào đang chờ xử lý.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td style="white-space:nowrap;">
                            <b>#<?php echo $r['id']; ?></b><br>
                            <span style="font-size:12.5px; color:#6b7280;"><?php echo date('d/m/Y H:i', strtotime($r['order_date'])); ?></span><br>
                            <span style="color:#cf3c3c; font-weight:600; font-size:13px;"><?php echo number_format($r['total_price'],0,',','.'); ?>đ</span>
                        </td>
                        <td class="customer-info">
                            <strong><?php echo htmlspecialchars($r['fullname_guest'] ?: ($r['registered_name'] ?: 'Khách lạ')); ?></strong><br>
                            <span><?php echo htmlspecialchars($r['email_guest']); ?></span>
                        </td>
                        <td style="font-size:13px; line-height:1.6;">
                            <?php echo $r['items_summary']; ?>
                        </td>
                        <td>
                            <div class="reason-box">
                                <?php echo nl2br(htmlspecialchars($r['cancel_reason'])); ?>
                            </div>
                        </td>
                        <td style="min-width: 120px;">
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Duyệt hủy đơn? Dữ liệu chi tiết đơn sẽ bị dọn dẹp và trả sản phẩm lại kho!');">
                                <?php echo csrf_tag(); ?>
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="order_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" class="btn-approve">Duyệt Hủy</button>
                            </form>
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Từ chối yêu cầu hủy của khách hàng?');">
                                <?php echo csrf_tag(); ?>
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="order_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" class="btn-reject">Từ Chối</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
