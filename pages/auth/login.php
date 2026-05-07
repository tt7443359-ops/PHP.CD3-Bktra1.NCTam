<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/guest_check.php';

$errors = [];
$email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $_SESSION['pending_action'] = $_GET['action'];
    $_SESSION['pending_id'] = intval($_GET['id']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email)) {
        $errors['email'] = __('error_email_empty');
    }
    if (empty($password)) {
        $errors['password'] = __('error_password_empty');
    }

    if (empty($errors)) {
        $safe_email = mysqli_real_escape_string($conn, $email);
        // Email
        $sql = "SELECT * FROM users WHERE email = '$safe_email' LIMIT 1";
        $res = mysqli_query($conn, $sql);

        if (mysqli_num_rows($res) > 0) {
            $user_data = mysqli_fetch_assoc($res);

            //Hash mật khẩu
            $is_password_valid = password_verify($password, $user_data['password']);
            // Migration: chỉ áp dụng nếu password trong DB là plain-text (chưa bcrypt)
            // Chặn exploit: dùng mã băm làm password để đăng nhập
            if (!$is_password_valid
                && !str_starts_with($user_data['password'], '$2y$')
                && $password === $user_data['password']) {
                $is_password_valid = true;
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $update_id = intval($user_data['id']);
                mysqli_query($conn, "UPDATE users SET password = '$new_hash' WHERE id = $update_id");
            }

            if ($is_password_valid) {
                if (isset($user_data['is_locked']) && $user_data['is_locked'] == 1) {
                    $locked_avatar = !empty($user_data['avatar']) ? $user_data['avatar'] : 'default_avatar.png';
                    $locked_name = !empty($user_data['fullname']) ? $user_data['fullname'] : $user_data['email'];
                    $errors['locked'] = [
                        'avatar' => $locked_avatar,
                        'name' => $locked_name,
                        'message' => __('error_account_locked')
                    ];
                } else {
                    if ($user_data['role'] == 'admin') {
                        //Admin
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_user'] = !empty($user_data['fullname']) ? $user_data['fullname'] : 'Admin';
                        $_SESSION['admin_email'] = $user_data['email'];
                        $_SESSION['admin_id'] = $user_data['id'];
                        mysqli_query($conn, "UPDATE users SET last_activity = NOW() WHERE id = " . intval($user_data['id']));
                        session_regenerate_id(true);
                        header("Location: " . $base_url . "admin/dashboard");
                        exit();
                    } else {
                        //Customer
                        $_SESSION["user"] = $user_data['email'];
                        $_SESSION["username"] = !empty($user_data['fullname']) ? $user_data['fullname'] : $user_data['email'];
                        $_SESSION["user_id"] = $user_data['id'];
                        mysqli_query($conn, "UPDATE users SET last_activity = NOW() WHERE id = " . intval($user_data['id']));
                        session_regenerate_id(true);

                        // Phục hồi giỏ hàng từ Cơ sở dữ liệu
                        if (!empty($user_data['cart_data'])) {
                            $saved_cart = json_decode($user_data['cart_data'], true);
                            if (is_array($saved_cart)) {
                                if (!isset($_SESSION['cart'])) {
                                    $_SESSION['cart'] = [];
                                }
                                foreach ($saved_cart as $pid => $qty) {
                                    if (isset($_SESSION['cart'][$pid])) {
                                        $_SESSION['cart'][$pid] += $qty;
                                    } else {
                                        $_SESSION['cart'][$pid] = $qty;
                                    }
                                }
                            }
                        }

                        // Hồi sinh cookie 
                        setcookie("stored_email", $user_data['email'], [
                            'expires' => time() + 86400,
                            'path' => '/',
                            'samesite' => 'Lax',
                            'httponly' => true
                        ]);
                        setcookie("stored_fullname", $_SESSION["username"], [
                            'expires' => time() + 86400,
                            'path' => '/',
                            'samesite' => 'Lax',
                            'httponly' => true
                        ]);

                        $redirect_url = $base_url . "shop";
                        if (isset($_SESSION['pending_action']) && isset($_SESSION['pending_id'])) {
                            $pending_action = $_SESSION['pending_action'];
                            $pending_id = $_SESSION['pending_id'];
                            
                            $check_sql = "SELECT stock_quantity FROM products WHERE id = $pending_id";
                            $run_check = mysqli_query($conn, $check_sql);
                            if (mysqli_num_rows($run_check) > 0) {
                                $stock = mysqli_fetch_assoc($run_check)['stock_quantity'];
                                if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
                                
                                if (isset($_SESSION['cart'][$pending_id])) {
                                    if ($_SESSION['cart'][$pending_id] < $stock) $_SESSION['cart'][$pending_id]++;
                                } else {
                                    if ($stock > 0) $_SESSION['cart'][$pending_id] = 1;
                                }
                                
                                $cart_json = json_encode($_SESSION['cart']);
                                mysqli_query($conn, "UPDATE users SET cart_data = '$cart_json' WHERE id = " . intval($user_data['id']));
                            }

                            if ($pending_action === 'buy_now') {
                                $redirect_url = $base_url . "cart";
                            } elseif ($pending_action === 'add_to_cart') {
                                $redirect_url = $base_url . "shop/details/" . $pending_id;
                            }
                            unset($_SESSION['pending_action'], $_SESSION['pending_id']);
                        }

                        header("Location: " . $redirect_url);
                        exit();
                    }
                }
            } else {
                $errors['login'] = __('error_login_failed');
            }
        } else {
            $errors['login'] = __('error_login_failed');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?php echo __('login_title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/login.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/auth_shared.css">
</head>
<body>

<div class="container fade-in-down">
    <h2><?php echo __('login_title'); ?></h2>
    <!-- Login Form -->
    
    <?php if (isset($_GET['error']) && $_GET['error'] === 'locked'): ?>
        <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 14px;">
            <?php echo __('error_session_locked'); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errors['locked'])): ?>
        <div class="locked-form-container">
            <div class="locked-account-msg" style="background: #ffebee; border: 1px solid #ffcdd2; border-radius: 8px; padding: 12px 15px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px;">
                <div style="position: relative; display: flex; flex-shrink: 0; width: 28px; height: 28px;">
                    <img src="<?php echo $base_url; ?>public/assets/img/avatarnorx.jpg" 
                         alt="Avatar" 
                         style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; opacity: 0.8; filter: grayscale(100%); background-color: #ccc; border: 1px solid #ddd;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                        <div style="width: 100%; height: 2px; background-color: #e53935; transform: rotate(-45deg); border-radius: 1px;"></div>
                    </div>
                </div>
                <div style="color: #c62828;">
                    <strong style="color: #b71c1c;"><?php echo htmlspecialchars($errors['locked']['name']); ?></strong> - <?php echo htmlspecialchars($errors['locked']['message']); ?>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end;">
                <a href="#" class="btn" style="display: inline-block; width: auto; padding: 8px 24px; background-color: #e53935; color: white; border-radius: 4px; font-size: 14px; text-decoration: none; font-weight: 500; transition: background 0.2s;"><?php echo __('restore_account'); ?></a>
            </div>
            
            <div class="links" style="margin-top: 20px;">
                <a href="<?php echo $base_url; ?>login">← <?php echo __('back_to_login'); ?></a>
            </div>
        </div>
    <?php else: ?>
        <?php if (isset($errors['login'])): ?>
            <p style="color: red; font-size: 0.8em;"><?php echo $errors['login']; ?></p>
        <?php endif; ?>

        <form method="POST" novalidate>
            <?php echo csrf_tag(); ?>
            <label><?php echo __('email_label'); ?></label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="<?php echo __('email_label'); ?>"
                required style="width: 100%;">
            <?php if (isset($errors['email'])): ?>
                <div style="color:red; font-size:13px; margin-top:5px; text-align:left;"><?php echo $errors['email']; ?></div>
            <?php endif; ?>

            <label><?php echo __('password_label'); ?></label>
            <div class="password-wrapper">
                <input type="password" name="password" id="login_pass" placeholder="<?php echo __('password_label'); ?>" required
                    style="width: 100%;">
                <span class="toggle-password" onclick="togglePass('login_pass', this)">
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
                <div style="color:red; font-size:13px; margin-top:5px; text-align:left;"><?php echo $errors['password']; ?></div>
            <?php endif; ?>

            <button type="submit" class="btn"><?php echo __('login_btn'); ?></button>
        </form>

        <div class="links">
            <?php echo __('no_account'); ?> <a href="<?php echo $base_url; ?>register"><?php echo __('register_title'); ?></a><br>
            <a href="<?php echo $base_url; ?>reset-password?retry=1"><?php echo __('forgot_password'); ?></a>
        </div>
    <?php endif; ?>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/auth.js"></script>

</body>

</html>