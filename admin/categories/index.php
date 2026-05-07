<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';
restrictToAdmin();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: " . $base_url . "admin/dashboard");
    exit();
}

if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

// Lấy danh sách danh mục
$result = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<?php
$page_title = "Quản Lý Danh Mục";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/admin_shared.css">

<div class="admin-wrapper">
<div class="admin-container">

    <div class="admin-header-action">
      <h2>Quản Lý Danh Mục</h2>
      <div class="action-buttons">
        <a href="<?php echo $base_url; ?>admin/create-category" class="btn-add">Thêm Danh Mục</a>
        <a href="<?php echo $base_url; ?>admin/products" class="btn-secondary">Quay lại</a>
      </div>
    </div>

    <!-- Danh sách -->
    <div class="admin-table-wrapper">
        <?php if (isset($error_msg)) echo "<p style='color:red; font-weight:bold; margin-top:0; margin-bottom:15px;'>$error_msg</p>"; ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 80px;">STT</th>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th style="width: 150px; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1;
                foreach ($categories as $c): ?>
                    <tr>
                        <td><strong><?php echo $stt++; ?></strong><br><small style="color:#888;">(#<?php echo $c['id']; ?>)</small></td>
                        <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($c['description']); ?></td>
                        <td>
                            <div class="action-group">
                                <a href="<?php echo $base_url; ?>admin/update-category/<?php echo $c['id']; ?>" class="btn-edit">Sửa</a>
                                <?php if ($c['id'] != 1): ?>
                                    <a href="<?php echo $base_url; ?>admin/delete-category/<?php echo $c['id']; ?>" onclick="return confirm('Xác nhận xóa danh mục này?');" class="btn-delete">Xóa</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</div>

<footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> ShopLIGHTNOVEL2X - Quản lý.
</footer>

</body>
</html>