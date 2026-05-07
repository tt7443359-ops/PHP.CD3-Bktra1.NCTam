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
$error_msg = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Lấy tham số filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xây dựng query — chỉ lấy user KHÔNG bị khóa
$where_conditions = ["is_locked = 0"];
$params = [];
$types = '';

if ($filter === 'admin') {
    $where_conditions[] = "role = 'admin'";
} elseif ($filter === 'customer') {
    $where_conditions[] = "role = 'customer'";
}

if (!empty($search)) {
    $where_conditions[] = "(fullname LIKE ? OR email LIKE ? OR id = ?)";
    $search_param = "%$search%";
    $params[] = &$search_param;
    $params[] = &$search_param;
    $params[] = &$search;
    $types .= 'ssi';
}

$where_clause = implode(' AND ', $where_conditions);
$sql = "SELECT * FROM users WHERE $where_clause ORDER BY id ASC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// kiểm tra & format trạng thái hoạt động dựa trên last_activity
function userStatus($last_activity)
{
    if (empty($last_activity)) {
        return '<span class="status-badge status-offline"><span class="dot"></span>Offline</span>';
    }

    // Parse timezone UTC+7
    $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
    $last = new DateTimeImmutable($last_activity, new DateTimeZone('Asia/Ho_Chi_Minh'));
    $diff = $now->getTimestamp() - $last->getTimestamp();

    // Dưới 60 giây = (online)
    if ($diff <= 60) {
        return '<span class="status-badge status-online"><span class="dot"></span>Đang hoạt động</span>';
    }

    // Offline
    if ($diff < 3600) {
        $label = floor($diff / 60) . ' phút trước';
    } elseif ($diff < 86400) {
        $label = floor($diff / 3600) . ' giờ trước';
    } elseif ($diff < 604800) {
        $label = floor($diff / 86400) . ' ngày trước';
    } elseif ($diff < 2592000) {
        $label = floor($diff / 604800) . ' tuần trước';
    } elseif ($diff < 31536000) {
        $label = floor($diff / 2592000) . ' tháng trước';
    } else {
        $label = floor($diff / 31536000) . ' năm trước';
    }

    return '<span class="status-badge status-offline"><span class="dot"></span>Hoạt động ' . $label . '</span>';
}
// Lấy số lượng tài khoản bị khóa để hiển thị badge
$locked_count_result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE is_locked = 1");
$locked_count = mysqli_fetch_assoc($locked_count_result)['cnt'] ?? 0;
?>

<?php
$page_title = "Quản Lý Người Dùng";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/admin_shared.css">

<div class="admin-wrapper">
    <div class="admin-container">

        <!-- Page Header -->
        <div class="admin-header-action">
            <div style="display:flex; align-items:center; gap: 10px;">
                <h2>Quản Lý Người Dùng</h2>
                <span style="color: #718096; font-size: 0.875rem;">(Tổng: <strong><?php echo count($users); ?></strong> tài khoản)</span>
            </div>
            <div class="action-buttons">
                <a href="<?php echo $base_url; ?>admin/locked-users" class="btn-primary" style="background: #6c757d;">
                    account
                    <?php if ($locked_count > 0): ?>
                        <span class="badge-count" style="margin-left:5px; background:white; color:#e53e3e; padding:2px 6px; border-radius:10px; font-size:12px;"><?php echo $locked_count; ?></span>
                    <?php endif; ?>
                </a>
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

        <!-- Search & Filter Bar -->
        <div class="toolbar" style="margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center; background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
            <form method="GET" action="" class="toolbar-form" style="display:flex; width:100%; gap:15px; align-items:center;">
                <div class="search-wrapper" style="display:flex; flex:1; align-items:center; border:1px solid #e2e8f0; border-radius:6px; padding:5px 10px;">
                    <input type="text" name="search" class="search-input" placeholder="Search..."
                        value="<?php echo htmlspecialchars($search); ?>" autocomplete="off" style="border:none; outline:none; padding:8px; width:100%;">
                    <?php if (!empty($search)): ?>
                        <a href="?filter=<?php echo $filter; ?>" class="search-clear" style="color:#a0aec0; text-decoration:none; padding:5px;">✕</a>
                    <?php endif; ?>
                    <button type="submit" style="border:none; background:transparent; padding:0; cursor:pointer; color:#718096; display:flex; align-items:center; justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </button>
                </div>
                <div style="flex-shrink: 0;">
                    <a href="<?php echo $base_url; ?>admin/create-user" style="display: inline-block; background: #64c5c5; color: #fff; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; transition: background 0.2s;">
                        Thêm người dùng
                    </a>
                </div>
                <div class="filter-dropdown">
                    <select name="filter" class="form-select" onchange="this.form.submit()"
                        style="padding: 9px 14px; border-radius: 6px; border: 1px solid #e2e8f0; outline: none; background-color: #f7fafc; cursor: pointer;">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Tất cả vai trò</option>
                        <option value="admin" <?php echo $filter === 'admin' ? 'selected' : ''; ?>>Quản trị</option>
                        <option value="customer" <?php echo $filter === 'customer' ? 'selected' : ''; ?>>Người dùng
                        </option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-user">Người dùng</th>
                        <th class="col-email">Email</th>
                        <th class="col-role">Vai trò</th>
                        <th class="col-status">Trạng thái</th>
                        <th class="col-action" style="text-align:center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="empty-state" style="text-align:center; padding:40px; color:#a0aec0;">
                                <div>Không tìm thấy người dùng nào</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <?php
                            $avatar = !empty($u['avatar']) ? $u['avatar'] : 'default_avatar.png';
                            $avatar_url = $base_url . 'public/uploads/avatars/' . $avatar;
                            $is_self = ($u['id'] == ($_SESSION['user']['id'] ?? 0));

                            // Initials fallback
                            $initials = '';
                            if (!empty($u['fullname'])) {
                                $parts = explode(' ', trim($u['fullname']));
                                $initials = mb_strtoupper(mb_substr(end($parts), 0, 1));
                                if (count($parts) > 1) {
                                    $initials = mb_strtoupper(mb_substr($parts[0], 0, 1)) . mb_strtoupper(mb_substr(end($parts), 0, 1));
                                }
                            }
                            ?>
                            <tr class="<?php echo $is_self ? 'row-self' : ''; ?>">
                                <td class="col-id">
                                    <span class="id-badge">#<?php echo htmlspecialchars($u['id']); ?></span>
                                </td>
                                <td class="col-user">
                                    <div class="user-cell">
                                        <div class="avatar-wrapper">
                                            <img src="<?php echo $avatar_url; ?>" alt="Avatar" class="user-avatar"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="avatar-initials" style="display:none;">
                                                <?php echo htmlspecialchars($initials ?: '?'); ?>
                                            </div>
                                        </div>
                                        <div class="user-info">
                                            <span class="user-name">
                                                <?php echo htmlspecialchars($u['fullname'] ?: 'Chưa có tên'); ?>
                                                <?php if ($is_self): ?>
                                                    <span class="self-tag">Bạn</span>
                                                <?php endif; ?>
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
                                <td class="col-status">
                                    <?php echo userStatus($u['last_activity']); ?>
                                </td>
                                <td class="col-action">
                                    <div class="action-group">
                                        <a href="<?php echo $base_url; ?>admin/update-user/<?php echo $u['id']; ?>"
                                            class="btn-edit" title="Chỉnh sửa">
                                            Sửa
                                        </a>
                                        <?php if (!$is_self): ?>
                                            <a href="<?php echo $base_url; ?>admin/lock-user/<?php echo $u['id']; ?>"
                                                class="btn-delete" title="Xóa tài khoản"
                                                onclick="return confirm('Xóa tài khoản của <?php echo htmlspecialchars(addslashes($u['fullname'] ?: $u['email'])); ?>?');">
                                                Xóa
                                            </a>
                                        <?php else: ?>
                                            <span class="btn-delete"
                                                title="Không thể khóa tài khoản của bạn" style="opacity: 0.5; cursor: not-allowed;">Xóa</span>
                                        <?php endif; ?>
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

<footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> ShopLIGHTNOVEL2X - Quản lý.
</footer>

</body>
</html>