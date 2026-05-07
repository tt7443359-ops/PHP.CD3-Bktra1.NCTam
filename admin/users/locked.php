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

// Xử lý thông báo
$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg   = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Lấy danh sách user bị khóa
$sql    = "SELECT * FROM users WHERE is_locked = 1 ORDER BY locked_at DESC";
$result = mysqli_query($conn, $sql);
$locked_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<?php
$page_title = "Tài Khoản Bị Khóa";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/admin_shared.css?v=<?php echo time(); ?>">

<div class="admin-wrapper">
    <div class="admin-container">

        <!-- Page Header -->
        <div class="admin-header-action">
            <div style="display:flex; align-items:center; gap: 10px;">
                <h2>Tài Khoản Bị Khóa</h2>
                <span style="color: #718096; font-size: 0.875rem;">(Tổng: <strong><?php echo count($locked_users); ?></strong> tài khoản)</span>
            </div>
            <div class="action-buttons">
                <a href="<?php echo $base_url; ?>admin/users" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($success_msg): ?>
            <div class="alert alert-success" style="margin-bottom: 20px; padding: 12px; background: #f0fff4; border: 1px solid #9ae6b4; border-radius: 8px; color: #276749;">
                <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error" style="margin-bottom: 20px; padding: 12px; background: #fff5f5; border: 1px solid #feb2b2; border-radius: 8px; color: #9b2c2c;">
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>
        <!-- Table -->
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-user">Người dùng</th>
                        <th class="col-email">Email</th>
                        <th class="col-role">Vai trò</th>
                        <th class="col-lock-info">Lý do / Ngày khóa</th>
                        <th class="col-action" style="text-align:center;">Hành động</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php if (empty($locked_users)): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <div>Không có tài khoản nào</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($locked_users as $u): ?>
                                <?php
                                $avatar     = !empty($u['avatar']) ? $u['avatar'] : 'default_avatar.png';
                                $avatar_url = $base_url . 'public/uploads/avatars/' . $avatar;

                                $initials = '';
                                if (!empty($u['fullname'])) {
                                    $parts    = explode(' ', trim($u['fullname']));
                                    $initials = mb_strtoupper(mb_substr(end($parts), 0, 1));
                                    if (count($parts) > 1) {
                                        $initials = mb_strtoupper(mb_substr($parts[0], 0, 1)) . mb_strtoupper(mb_substr(end($parts), 0, 1));
                                    }
                                }
                                ?>
                                <tr class="row-locked">
                                    <td class="col-id">
                                        <span class="id-badge">#<?php echo htmlspecialchars($u['id']); ?></span>
                                    </td>
                                    <td class="col-user">
                                        <div class="user-cell">
                                            <div class="avatar-wrapper avatar-locked">
                                                <img
                                                    src="<?php echo $avatar_url; ?>"
                                                    alt="Avatar"
                                                    class="user-avatar"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                >
                                                <div class="avatar-initials" style="display:none;">
                                                    <?php echo htmlspecialchars($initials ?: '?'); ?>
                                                </div>
                                            </div>
                                            <div class="user-info">
                                                <span class="user-name">
                                                    <?php echo htmlspecialchars($u['fullname'] ?: 'Chưa có tên'); ?>
                                                </span>
                                                <?php if (!empty($u['phone'])): ?>
                                                    <span class="user-phone"><?php echo htmlspecialchars($u['phone']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-email">
                                        <span class="email-text"><?php echo htmlspecialchars($u['email']); ?></span>
                                    </td>
                                    <td class="col-role">
                                        <?php if ($u['role'] === 'admin'): ?>
                                            <span class="role-badge role-admin">Admin</span>
                                        <?php else: ?>
                                            <span class="role-badge role-user">User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-lock-info">
                                        <div class="lock-reason">
                                            <?php echo htmlspecialchars($u['locked_reason'] ?: 'Không rõ lý do'); ?>
                                        </div>
                                        <?php if (!empty($u['locked_at'])): ?>
                                            <div class="lock-date">
                                                 <?php echo date('d/m/Y H:i', strtotime($u['locked_at'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-action">
                                        <div class="action-group">
                                            <a href="<?php echo $base_url; ?>admin/restore-user/<?php echo $u['id']; ?>"
                                               class="btn-edit"
                                               title="Khôi phục tài khoản"
                                               onclick="return confirm('Khôi phục tài khoản của <?php echo htmlspecialchars(addslashes($u['fullname'] ?: $u['email'])); ?>?');">
                                                Khôi phục
                                            </a>
                                            <a href="<?php echo $base_url; ?>admin/delete-user/<?php echo $u['id']; ?>"
                                               class="btn-delete"
                                               title="Xóa vĩnh viễn"
                                               onclick="return confirm('Xóa tài khoản <?php echo htmlspecialchars(addslashes($u['fullname'] ?: $u['email'])); ?>? Không thể hoàn tác!');">
                                                Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> ShopLIGHTNOVEL2X - Quản lý.
</footer>

</body>
</html>
