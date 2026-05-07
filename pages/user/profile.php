<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if (!isset($_SESSION['user']) && !isset($_SESSION['admin_email'])) {
    header("Location: " . $base_url . "login");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['user'] ?? $_SESSION['admin_email']);

$success_msg = "";
$error_msg = "";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profile'])) {
        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($fullname)) {
            $errors['fullname'] = __('fullname_empty');
        }

        $avatar_query = "";
        if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == 0) {
            $target_dir = __DIR__ . "/../../public/uploads/avatars/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_ext = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
            $new_filename = md5(time() . rand()) . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;

            if (in_array($file_ext, ["jpg", "png", "jpeg", "gif"])) {
                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    $avatar_query = ", avatar = '" . mysqli_real_escape_string($conn, $new_filename) . "'";
                } else {
                    $errors['avatar'] = __('avatar_upload_error');
                }
            } else {
                $errors['avatar'] = __('avatar_format_error');
            }
        }

        if (empty($errors)) {
            $fn_db = mysqli_real_escape_string($conn, $fullname);
            $ph_db = mysqli_real_escape_string($conn, $phone);
            $ad_db = mysqli_real_escape_string($conn, $address);

            $sql_update = "UPDATE users SET fullname = '$fn_db', phone = '$ph_db', address = '$ad_db' $avatar_query WHERE email = '$email'";
            if (mysqli_query($conn, $sql_update)) {
            } else {
                $errors['db'] = __('db_error');
            }
        }
    }
}

// Lấy lại thông tin DB
$info_res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
$customer = mysqli_fetch_assoc($info_res);
$user_id = $customer['id'];

// Kiểm tra xem đã có avatar tùy chỉnh chưa
$has_custom_avatar = !empty($customer['avatar']) && $customer['avatar'] !== 'default_avatar.png' && file_exists(__DIR__ . "/../../public/uploads/avatars/" . $customer['avatar']);

$avatar_src = $has_custom_avatar 
    ? $base_url . "public/uploads/avatars/" . $customer['avatar']
    : $base_url . "public/assets/img/default_avatar.png";

// Đếm trạng thái đơn hàng
$status_counts = ['chờ xác nhận' => 0, 'đã xác nhận' => 0, 'đang giao' => 0, 'đã giao' => 0, 'huỷ' => 0];
$stat_res = mysqli_query($conn, "SELECT status, COUNT(id) as cnt FROM orders WHERE user_id = $user_id GROUP BY status");
while ($row = mysqli_fetch_assoc($stat_res)) {
    $status_counts[$row['status']] = $row['cnt'];
}
?>

<?php
$page_title = __('my_profile');
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/profile.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/map_picker.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="profile-layout-store">
    <div class="profile-sidebar-store">
        <div class="user-intro-store">
            <img src="<?php echo $avatar_src; ?>" alt="Avatar" class="avatar-mini-store">
            <div class="user-intro-text-store">
                <div class="user-name-store"><?php echo htmlspecialchars($customer['fullname'] ? $customer['fullname'] : $customer['email']); ?></div>
                <div class="edit-profile-store"><i class="fa-solid fa-pen"></i> <?php echo __('profile'); ?></div>
            </div>
        </div>
    </div>
    
    <div class="profile-main-store">
        <div class="profile-header-store">
            <h2><?php echo __('my_profile'); ?></h2>
            <p><?php echo __('manage_profile_desc'); ?></p>
        </div>
        
        <div class="profile-body-store">
            <div class="profile-form-store">
                <?php if ($success_msg): ?>
                    <p class="msg-success"><?php echo $success_msg; ?></p>
                <?php endif; ?>
                <?php if (isset($errors['db'])): ?>
                    <p class="msg-error"><?php echo $errors['db']; ?></p>
                <?php endif; ?>

                <form method="POST" action="<?php echo $base_url; ?>profile" enctype="multipart/form-data" id="profileForm">
                    <?php echo csrf_tag(); ?>
                    
                    <div class="store-form-group">
                        <label><?php echo __('username'); ?></label>
                        <div class="store-input-wrap">
                            <input type="text" value="<?php echo htmlspecialchars($customer['email']); ?>" disabled
                                style="background:#f4f4f4; cursor:not-allowed;" class="form-control-store">
                        </div>
                    </div>

                    <div class="store-form-group">
                        <label><?php echo __('name_label'); ?></label>
                        <div class="store-input-wrap">
                            <input type="text" name="fullname"
                                value="<?php echo htmlspecialchars($customer['fullname'] ?? ''); ?>" required class="form-control-store">
                            <?php if (isset($errors['fullname'])): ?>
                                <div style="color:red; font-size:13px; margin-top:5px;"><?php echo $errors['fullname']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="store-form-group">
                        <label><?php echo __('phone_label'); ?></label>
                        <div class="store-input-wrap">
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>"
                                placeholder="<?php echo __('not_updated_yet'); ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control-store">
                        </div>
                    </div>

                    <div class="store-form-group">
                        <label><?php echo __('address_label'); ?></label>
                        <div class="store-input-wrap">
                            <div class="address-field-wrap" style="position: relative;">
                                <textarea name="address" id="address" rows="3"
                                    placeholder="<?php echo __('not_updated_yet'); ?>" class="form-control-store"><?php echo htmlspecialchars($customer['address']); ?></textarea>
                                <button type="button" class="btn-open-map" onclick="openMapModal(event)" title="<?php echo __('select_on_map'); ?>" style="position: absolute; right: 10px; top: 10px; border: none; background: transparent; color: #007bff; cursor: pointer; font-size: 16px;">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="store-form-group">
                        <label></label>
                        <div class="store-input-wrap">
                            <button type="submit" name="update_profile" class="btn-save" style="width: auto; padding: 10px 20px;"><?php echo __('save_btn'); ?></button>
                        </div>
                    </div>

                    <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                </form>
            </div>
            
            <div class="profile-avatar-store">
                <div class="avatar-preview-wrap">
                    <img id="avatar-preview-store" src="<?php echo $avatar_src; ?>" alt="Avatar">
                </div>
                <button type="button" class="btn-select-image-store" onclick="document.getElementById('avatarInput').click()"><?php echo __('select_image'); ?></button>
                <div class="avatar-desc-store">
                    <?php echo __('max_file_size'); ?><br>
                    <?php echo __('file_format'); ?>
                </div>
                <?php if (isset($errors['avatar'])): ?>
                    <div style="color:red; font-size:13px; margin-top:5px; text-align: center;"><?php echo $errors['avatar']; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



<?php require_once __DIR__ . '/../../includes/map_modal.php'; ?>

<script src="<?php echo $base_url; ?>public/assets/js/image_preview.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/map_picker.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/profile.js"></script>
</body>

</html>