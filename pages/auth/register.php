<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/guest_check.php';

$errors = [];
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // 1. Kiểm tra các trường trống và định dạng
    if (empty($email)) {
        $errors['email'] = __('error_email_empty');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = __('error_email_invalid');
    }

    // 2. Chặn trùng mail
    if (empty($errors)) {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $check_mail = "SELECT id FROM users WHERE email = '$safe_email' LIMIT 1";
        $result = mysqli_query($conn, $check_mail);
        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = __('error_email_exists');
        }
    }

    // 3. Kiểm tra Mật khẩu
    if (empty($password)) {
        $errors['password'] = __('error_password_empty');
    } elseif (strlen($password) < 6) {
        $errors['password'] = __('error_password_length');
    }
    if ($password !== $confirm) {
        $errors['confirm_password'] = __('error_password_mismatch');
    }

    // 4. Lưu vào csdl và cookie
    if (empty($errors)) {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $safe_hashed_password = mysqli_real_escape_string($conn, $hashed_password);

        mysqli_begin_transaction($conn);
        try {
            $sql_user = "INSERT INTO users (email, password, role, fullname) VALUES ('$safe_email', '$safe_hashed_password', 'customer', '')";
            mysqli_query($conn, $sql_user);

            mysqli_commit($conn);

            // Set Cookie (Chỉ giữ email để tiện nhập liệu)
            setcookie("stored_email", $email, [
                'expires' => time() + 86400,
                'path' => '/',
                'samesite' => 'Lax',
                'httponly' => true
            ]);

            header("Location: " . $base_url . "login?msg=success");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors['system'] = "Lỗi hệ thống, vui lòng thử lại!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?php echo __('register_title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/register.css">
</head>
<style>
    .password-wrapper {
        position: relative;
        width: 100%;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        font-size: 18px;
        user-select: none;
    }

    input[type="password"],
    input[type="text"] {
        padding-right: 40px !important;
        box-sizing: border-box;
    }
</style>
<body>

<div class="container fade-in-down">
    <h2><?php echo __('register_title'); ?></h2>
    <?php if (!empty($success_msg)): ?>
        <p class="success-msg"><?php echo $success_msg; ?></p>
    <?php endif; ?>

    <form action="" method="POST" novalidate>
        <?php echo csrf_tag(); ?>

        <label><?php echo __('email_label'); ?></label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="<?php echo __('email_label'); ?>"
            required style="width: 100%;">
        <?php if (isset($errors['email'])): ?>
            <div style="color:red; font-size:13px; margin-top:5px; text-align:left;"><?php echo $errors['email']; ?></div>
        <?php endif; ?>

        <label><?php echo __('password_label'); ?></label>
        <div class="password-wrapper">
            <input type="password" name="password" id="reg_pass" placeholder="<?php echo __('password_label'); ?>" required
                style="width: 100%;">
            <span class="toggle-password" onclick="togglePass('reg_pass', this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                    </path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
            </span>
        </div>
        <?php if (isset($errors['password'])): ?>
            <div style="color:red; font-size:13px; margin-top:5px; text-align:left;"><?php echo $errors['password']; ?>
            </div><?php endif; ?>

        <label><?php echo __('confirm_password'); ?></label>
        <div class="password-wrapper">
            <input type="password" name="confirm_password" id="reg_confirm" placeholder="<?php echo __('confirm_password'); ?>" required
                style="width: 100%;">
            <span class="toggle-password" onclick="togglePass('reg_confirm', this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                    </path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
            </span>
        </div>
        <?php if (isset($errors['confirm_password'])): ?>
            <div style="color:red; font-size:13px; margin-top:5px; text-align:left;">
                <?php echo $errors['confirm_password']; ?></div><?php endif; ?>

        <button type="submit" class="btn"><?php echo __('register_title'); ?></button>
    </form>

    <div class="links">
        <?php echo __('already_have_account'); ?> <a href="<?php echo $base_url; ?>login"><?php echo __('login_title'); ?></a><br>
        <a href="<?php echo $base_url; ?>reset-password"><?php echo __('forgot_password'); ?></a>
    </div>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/auth.js"></script>

</body>

</html>