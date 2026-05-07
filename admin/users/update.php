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

// Lấy ID user cần sửa
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($user_id <= 0) {
    $_SESSION['error_msg'] = "ID người dùng không hợp lệ";
    header("Location: " . $base_url . "admin/users");
    exit();
}

// Lấy thông tin user
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $_SESSION['error_msg'] = "Không tìm thấy người dùng";
    header("Location: " . $base_url . "admin/users");
    exit();
}

// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = $user['email']; // Khóa email, không nhận từ form nữa
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role = $_POST['role'] ?? 'customer';

    // Validate
    $errors = [];
    if (empty($fullname))
        $errors[] = "Họ tên không được để trống";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Email không hợp lệ";
    if (!in_array($role, ['admin', 'customer']))
        $errors[] = "Vai trò không hợp lệ";

    // Kiểm tra email trùng (trừ user hiện tại)
    if (empty($errors)) {
        $stmt_check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($stmt_check, "si", $email, $user_id);
        mysqli_stmt_execute($stmt_check);
        $check_result = mysqli_stmt_get_result($stmt_check);
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Email đã được sử dụng bởi người khác";
        }
    }

    // Xử lý upload avatar
    $avatar = $user['avatar']; // Giữ avatar cũ
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../public/uploads/avatars/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB

        $file_type = $_FILES['avatar']['type'];
        $file_size = $_FILES['avatar']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WebP)";
        } elseif ($file_size > $max_size) {
            $errors[] = "Kích thước ảnh không được vượt quá 2MB";
        } else {
            // Xóa avatar cũ nếu không phải default
            if (!empty($user['avatar']) && $user['avatar'] !== 'default_avatar.png') {
                $old_avatar_path = $upload_dir . $user['avatar'];
                if (file_exists($old_avatar_path)) {
                    unlink($old_avatar_path);
                }
            }

            // Tạo tên file mới
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $avatar = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = $upload_dir . $avatar;

            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                $errors[] = "Không thể upload ảnh";
                $avatar = $user['avatar']; // Revert
            }
        }
    }

    if (empty($errors)) {
        // Update database
        $stmt_update = mysqli_prepare(
            $conn,
            "UPDATE users SET fullname = ?, email = ?, phone = ?, address = ?, role = ?, avatar = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt_update, "ssssssi", $fullname, $email, $phone, $address, $role, $avatar, $user_id);

        if (mysqli_stmt_execute($stmt_update)) {
            header("Location: " . $base_url . "admin/users");
            exit();
        } else {
            $errors[] = "Lỗi khi cập nhật: " . mysqli_error($conn);
        }
    }
}

// Lấy thông báo lỗi
$form_errors = $errors ?? [];
?>
<?php
// Hàm hiển thị trạng thái dựa trên last_activity (dùng riêng cho trang này)
function getUserActivityStatus($last_activity)
{
    if (empty($last_activity)) {
        return ['label' => 'Offline', 'class' => 'text-muted'];
    }
    $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
    $last = new DateTimeImmutable($last_activity, new DateTimeZone('Asia/Ho_Chi_Minh'));
    $diff = $now->getTimestamp() - $last->getTimestamp();
    if ($diff <= 60) {
        return ['label' => 'Đang hoạt động', 'class' => 'text-success'];
    }
    if ($diff < 3600)
        $t = floor($diff / 60) . ' phút';
    elseif ($diff < 86400)
        $t = floor($diff / 3600) . ' giờ';
    elseif ($diff < 604800)
        $t = floor($diff / 86400) . ' ngày';
    elseif ($diff < 2592000)
        $t = floor($diff / 604800) . ' tuần';
    elseif ($diff < 31536000)
        $t = floor($diff / 2592000) . ' tháng';
    else
        $t = floor($diff / 31536000) . ' năm';
    return ['label' => 'Hoạt động ' . $t . ' trước', 'class' => 'text-muted'];
}
$activity_status = getUserActivityStatus($user['last_activity'] ?? null);
?>
<?php
$page_title = "Sửa Người Dùng";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/admin_shared.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/profile.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/map_picker.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="users-page" style="background: transparent; padding-top: 0;">
    <div class="users-container" style="box-shadow: none; background: transparent; padding: 0; max-width: 1200px;">

        <?php if (!empty($form_errors)): ?>
            <div class="alert alert-error" style="max-width: 1200px; margin: 0 auto 20px;">
                <?php foreach ($form_errors as $err): ?>
                    <div><?php echo htmlspecialchars($err); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php
        $has_custom_avatar = !empty($user['avatar']) && $user['avatar'] !== 'default_avatar.png' && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $user['avatar']);
        $avatar = $has_custom_avatar ? $user['avatar'] : 'default_avatar.png';
        $avatar_path = $base_url . 'public/uploads/avatars/' . $avatar;
        ?>

        <div class="profile-layout-store" style="margin: 0 auto;">
            <div class="profile-sidebar-store">
                <div class="user-intro-store">
                    <img src="<?php echo $avatar_path; ?>" alt="Avatar" class="avatar-mini-store"
                        onerror="this.src='<?php echo $base_url; ?>public/assets/img/default_avatar.png';">
                    <div class="user-intro-text-store">
                        <div class="user-name-store">
                            <?php echo htmlspecialchars($user['fullname'] ?: $user['email']); ?>
                        </div>
                        <div class="edit-profile-store"><i class="fa-solid fa-user-pen"></i></div>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <a href="<?php echo $base_url; ?>admin/users" class="btn-store-outline"
                        style="display:block; text-align:center; padding: 8px 0; width: 100%; box-sizing: border-box;">←</a>
                </div>
            </div>

            <div class="profile-main-store">
                <div class="profile-header-store">
                    <h2>Chi tiết người dùng #<?php echo htmlspecialchars($user_id); ?></h2>
                    <p>Cập nhật thông tin và phân quyền người dùng</p>
                </div>

                <div class="profile-body-store">
                    <div class="profile-form-store">
                        <form method="POST" enctype="multipart/form-data" id="profileForm">
                            <?php echo csrf_tag(); ?>

                            <div class="store-form-group">
                                <label>Email Đăng Nhập</label>
                                <div class="store-input-wrap">
                                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                                        style="background:#f4f4f4; cursor:not-allowed;" class="form-control-store">
                                </div>
                            </div>

                            <div class="store-form-group">
                                <label>Tên (Họ Tên)</label>
                                <div class="store-input-wrap">
                                    <input type="text" name="fullname"
                                        value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required
                                        class="form-control-store">
                                </div>
                            </div>

                            <div class="store-form-group">
                                <label>Số Điện Thoại</label>
                                <div class="store-input-wrap">
                                    <input type="tel" name="phone"
                                        value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                        placeholder="Chưa cập nhật"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                        class="form-control-store">
                                </div>
                            </div>

                            <div class="store-form-group">
                                <label>Địa Chỉ</label>
                                <div class="store-input-wrap">
                                    <div class="address-field-wrap" style="position: relative;">
                                        <textarea name="address" id="address" rows="3" placeholder="Chưa cập nhật"
                                            class="form-control-store"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                        <button type="button" class="btn-open-map" onclick="openMapModal(event)"
                                            title="Chọn trên bản đồ"
                                            style="position: absolute; right: 10px; top: 10px; border: none; background: transparent; color: #ee4d2d; cursor: pointer; font-size: 16px;">
                                            <i class="fa-solid fa-map-location-dot"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="store-form-group">
                                <label>Vai Trò</label>
                                <div class="store-input-wrap">
                                    <select name="role" class="form-control-store">
                                        <option value="customer" <?php echo ($user['role'] ?? 'customer') === 'customer' ? 'selected' : ''; ?>>Người dùng</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>
                                            Quản trị</option>
                                    </select>
                                </div>
                            </div>

                            <div class="store-form-group">
                                <label></label>
                                <div class="store-input-wrap">
                                    <button type="submit" class="btn-store-solid"
                                        style="padding: 10px 20px; background: #007bff; color: white; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s;">Lưu</button>
                                </div>
                            </div>

                            <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display: none;"
                                onchange="previewAvatar(this)">
                        </form>
                    </div>

                    <div class="profile-avatar-store">
                        <div class="avatar-preview-wrap">
                            <img id="avatar-preview-store" src="<?php echo $avatar_path; ?>" alt="Avatar"
                                onerror="this.src='<?php echo $base_url; ?>public/assets/img/default_avatar.png';">
                        </div>
                        <button type="button" class="btn-select-image-store"
                            onclick="document.getElementById('avatarInput').click()">Chọn Ảnh</button>
                        <div class="avatar-desc-store">
                            Dụng lượng file tối đa 2 MB<br>
                            Định dạng: .JPEG, .PNG, .WEBP
                        </div>

                        <div class="system-info-box"
                            style="margin-top: 30px; width: 100%; text-align: left; background: #f9f9f9; padding: 15px; border-radius: 4px; border: 1px solid #efefef;">
                            <div style="font-weight: 600; margin-bottom: 10px; font-size: 14px; color: #333;">Thông tin
                                hệ thống</div>
                            <div style="font-size: 13px; color: #555; margin-bottom: 8px;">Ngày tạo: <span
                                    style="color:#333; float:right;"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                            </div>
                            <div style="font-size: 13px; color: #555;">Trạng thái: <span
                                    class="<?php echo !empty($user['is_locked']) ? 'text-danger' : $activity_status['class']; ?>"
                                    style="font-weight: 600; float:right;"><?php echo !empty($user['is_locked']) ? 'Đã khóa' : htmlspecialchars($activity_status['label']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<?php require_once __DIR__ . '/../../includes/map_modal.php'; ?>

<script src="<?php echo $base_url; ?>public/assets/js/image_preview.js"></script>

<script src="<?php echo $base_url; ?>public/assets/js/map_picker.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/admin.js"></script>
</body>

</html>