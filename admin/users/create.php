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

$errors = [];
$email = "";
$role = "customer";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'customer';

    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không đúng định dạng.";
    } else {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $check_mail = "SELECT id FROM users WHERE email = '$safe_email' LIMIT 1";
        $result = mysqli_query($conn, $check_mail);
        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = "Email này đã tồn tại trong hệ thống.";
        }
    }

    if (empty($password)) {
        $errors['password'] = "Vui lòng nhập mật khẩu.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }

    if (!in_array($role, ['admin', 'customer'])) {
        $role = 'customer';
    }

    if (empty($errors)) {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $safe_hashed_password = mysqli_real_escape_string($conn, $hashed_password);

        $sql = "INSERT INTO users (email, password, role, fullname) VALUES ('$safe_email', '$safe_hashed_password', '$role', '')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_msg'] = "Thêm người dùng mới thành công!";
            header("Location: " . $base_url . "admin/users");
            exit();
        } else {
            $errors['system'] = "Lỗi CSDL: " . mysqli_error($conn);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Người Dùng Mới</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/register.css">
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
    input[type="text"],
    input[type="email"],
    select {
        padding-right: 40px !important;
        box-sizing: border-box;
    }
    select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        margin-bottom: 20px;
        background-color: #fff;
    }
</style>
</head>
<body>
<div class="container fade-in-down">
    <h2>Thêm Người Dùng Mới</h2>
        <?php if (isset($errors['system'])): ?>
            <p style="color:red; text-align:center;"><?php echo $errors['system']; ?></p>
        <?php endif; ?>

        <form action="" method="POST" novalidate>
            <?php echo csrf_tag(); ?>
            
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Nhập email" required style="width: 100%;">
            <?php if (isset($errors['email'])): ?>
                <div style="color:red; font-size:13px; margin-top:5px; margin-bottom:15px; text-align:left;"><?php echo $errors['email']; ?></div>
            <?php endif; ?>

            <label>Mật khẩu</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="reg_pass" placeholder="Nhập mật khẩu" required style="width: 100%;">
                <span class="toggle-password" onclick="togglePass('reg_pass', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </span>
            </div>
            <?php if (isset($errors['password'])): ?>
                <div style="color:red; font-size:13px; margin-top:5px; margin-bottom:15px; text-align:left;"><?php echo $errors['password']; ?></div>
            <?php endif; ?>

            <label>Vai trò</label>
            <select name="role">
                <option value="customer" <?php echo $role === 'customer' ? 'selected' : ''; ?>>Người dùng</option>
                <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Quản trị</option>
            </select>

            <button type="submit" class="btn" style="margin-top: 10px;">Thêm</button>
        </form>
    </div>

<script src="<?php echo $base_url; ?>public/assets/js/auth.js"></script>
</body>
</html>
