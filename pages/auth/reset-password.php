<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/guest_check.php';

$errors = [];
$msg = "";
$step = $_SESSION['reset_step'] ?? 1;
$email_session = $_SESSION['reset_email'] ?? '';

// -- Xử lí quay lại/làm mới --
if (isset($_GET['retry'])) {
    unset($_SESSION['reset_step'], $_SESSION['reset_email'], $_SESSION['reset_code_shown']);
    header("Location: " . $base_url . "reset-password");
    exit();
}

// -- Bước 1: Xác minh email/sdt--
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['step1'])) {
    $identity = mysqli_real_escape_string($conn, trim($_POST['identity'] ?? ''));

    if (empty($identity))
        $errors['identity'] = __('enter_email_or_phone');

    if (empty($errors)) {
        //user thỏa mãn 1 trong 2 điều kiện
        $check_user = mysqli_query($conn, "SELECT id, email FROM users WHERE email = '$identity' OR phone = '$identity' LIMIT 1");
        if ($user_row = mysqli_fetch_assoc($check_user)) {
            $email = $user_row['email'];
            // Khởi tạo mã
            $otp = rand(100000, 999999);
            $expire_time = date('Y-m-d H:i:s', time() + 30); // Hạn 30s

            mysqli_query($conn, "UPDATE users SET reset_token = '$otp', reset_expire = '$expire_time' WHERE id = " . $user_row['id']);

            $_SESSION['reset_step'] = 2;
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code_shown'] = $otp;
            header("Location: " . $base_url . "reset-password");
            exit();
        } else {
            $errors['auth'] = __('error_no_account');
        }
    }
}

// -- Bước 2: Xác thực--
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['step2'])) {
    $otp_input = implode('', $_POST['otp'] ?? []);

    $check_otp = mysqli_query($conn, "SELECT id, reset_expire FROM users WHERE email = '$email_session' AND reset_token = '$otp_input' LIMIT 1");
    if ($row = mysqli_fetch_assoc($check_otp)) {
        $expire = strtotime($row['reset_expire']);
        if (time() <= $expire) {
            $_SESSION['reset_step'] = 3;
            header("Location: " . $base_url . "reset-password");
            exit();
        } else {
            $errors['otp'] = __('error_otp_expired');
        }
    } else {
        $errors['otp'] = __('error_otp_incorrect');
    }
}

// -- Xử lí gửi lại mã trực tiếp --
if (isset($_GET['resend']) && $email_session) {
    // Kiểm tra xem đã hết 30s chưa (dựa vào session cũ)
    $check_old = mysqli_query($conn, "SELECT reset_expire FROM users WHERE email = '$email_session' LIMIT 1");
    $old_row = mysqli_fetch_assoc($check_old);
    if ($old_row && time() < strtotime($old_row['reset_expire'])) {
        echo "<script>alert('" . __('resend_wait') . "'); window.location.href='" . $base_url . "reset-password';</script>";
        exit();
    }

    $otp = rand(100000, 999999);
    $expire_time = date('Y-m-d H:i:s', time() + 30);
    mysqli_query($conn, "UPDATE users SET reset_token = '$otp', reset_expire = '$expire_time' WHERE email = '$email_session'");

    $_SESSION['reset_code_shown'] = $otp;
    header("Location: " . $base_url . "reset-password");
    exit();
}

// -- Đổi mật khẩu mới--
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['step3'])) {
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (strlen($new_pass) < 6)
        $errors['new_password'] = __('error_password_length');
    if ($new_pass !== $confirm_pass)
        $errors['confirm_password'] = __('error_password_mismatch');

    if (empty($errors)) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed_pass', reset_token = NULL, reset_expire = NULL WHERE email = '$email_session'");

        unset($_SESSION['reset_step'], $_SESSION['reset_email'], $_SESSION['reset_code_shown']);
        echo "<script>alert('" . __('success_reset') . "'); window.location.href='" . $base_url . "login';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?php echo __('reset_password_title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/reset-password.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/auth_shared.css">
</head>

<body>

    <div class="container">
        <h2><?php echo __('find_account'); ?></h2>

        <?php if ($step == 1): ?>
            <?php if (isset($errors['auth'])): ?>
                <div style="color:red; text-align:center; margin-bottom:10px;"><?php echo $errors['auth']; ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <?php echo csrf_tag(); ?>
                <input type="hidden" name="step1" value="1">
                <label><?php echo __('email_or_phone'); ?></label>
                <input type="text" name="identity" required placeholder="<?php echo __('enter_email_or_phone'); ?>">
                <?php if (isset($errors['identity'])): ?>
                    <div style="color:red; font-size:12px; margin-top:5px; text-align:left;"><?php echo $errors['identity']; ?></div>
                <?php endif; ?>

                <button type="submit" class="btn"><?php echo __('next_step'); ?></button>
            </form>

        <?php elseif ($step == 2): ?>
            <div class="code-box" onclick="revealCode()" style="cursor: pointer;" title="Nhấn để xem mã">
                <p id="codeInstruction"><?php echo __('otp_instruction'); ?></p>
                <div class="code-reveal" id="otpCode">******</div>
            </div>

            <div class="timer-box" id="timer"><?php echo __('timer_prefix'); ?> 30s</div>

            <form method="POST" id="otpForm">
                <?php echo csrf_tag(); ?>
                <input type="hidden" name="step2" value="1">
                <div class="otp-inputs">
                    <input type="text" name="otp[]" maxlength="1" onkeyup="moveFocus(this, 1)" id="otp1" autocomplete="off">
                    <input type="text" name="otp[]" maxlength="1" onkeyup="moveFocus(this, 2)" id="otp2" autocomplete="off">
                    <input type="text" name="otp[]" maxlength="1" onkeyup="moveFocus(this, 3)" id="otp3" autocomplete="off">
                    <input type="text" name="otp[]" maxlength="1" onkeyup="moveFocus(this, 4)" id="otp4" autocomplete="off">
                    <input type="text" name="otp[]" maxlength="1" onkeyup="moveFocus(this, 5)" id="otp5" autocomplete="off">
                    <input type="text" name="otp[]" maxlength="1" onkeyup="moveFocus(this, 6)" id="otp6" autocomplete="off">
                </div>
                <?php if (isset($errors['otp'])): ?>
                    <div style="color:red; text-align:center; font-size:13px;"><?php echo $errors['otp']; ?></div>
                <?php endif; ?>

                <button type="submit" class="btn" id="btnSubmit"><?php echo __('verify_otp'); ?></button>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a href="?resend=1" class="btn" id="btnResend"
                        style="flex:1; text-decoration:none; text-align:center; background: #3b82f6; margin:0;"><?php echo __('resend_otp'); ?></a>
                    <a href="?retry=1" class="btn"
                        style="flex:1; text-decoration:none; text-align:center; background: #94a3b8; margin:0;"><?php echo __('retry_identity'); ?></a>
                </div>
            </form>

        <?php elseif ($step == 3): ?>
            <form method="POST" novalidate>
                <?php echo csrf_tag(); ?>
                <input type="hidden" name="step3" value="1">
                <label><?php echo __('new_password'); ?></label>
                <div class="password-wrapper">
                    <input type="password" id="new_password" name="new_password" required placeholder="<?php echo __('error_password_length'); ?>">
                    <span class="toggle-password" onclick="togglePass('new_password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                            </path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </span>
                </div>
                <?php if (isset($errors['new_password'])): ?>
                    <div style="color:red; font-size:12px; margin-top:5px; text-align:left;"><?php echo $errors['new_password']; ?></div>
                <?php endif; ?>

                <label><?php echo __('confirm_password'); ?></label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" required
                        placeholder="<?php echo __('confirm_password'); ?>">
                    <span class="toggle-password" onclick="togglePass('confirm_password', this)">
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
                    <div style="color:red; font-size:12px; margin-top:5px; text-align:left;"><?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>

                <button type="submit" class="btn"><?php echo __('reset_complete_btn'); ?></button>
            </form>
        <?php endif; ?>

        <div class="links">
            <a href="login"><?php echo __('back_to_login'); ?></a>
        </div>
    </div>

    <script src="<?php echo $base_url; ?>public/assets/js/auth.js"></script>
    <script>
        // Logic đếm ngược đồng bộ với CSDL
        <?php if ($step == 2):
            $check_time = mysqli_query($conn, "SELECT reset_expire FROM users WHERE email = '$email_session' LIMIT 1");
            $time_row = mysqli_fetch_assoc($check_time);
            $remaining = 0;
            if ($time_row) {
                $remaining = strtotime($time_row['reset_expire']) - time();
            }
            $remaining = max(0, $remaining);
            ?>
            const remainingTime = <?php echo $remaining; ?>;
            const otpCode = "<?php echo $_SESSION['reset_code_shown']; ?>";
        <?php endif; ?>
    </script>
    <script src="<?php echo $base_url; ?>public/assets/js/reset_password.js?v=<?php echo time(); ?>"></script>

</body>

</html>