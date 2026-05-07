<?php
require_once __DIR__ . '/../includes/db.php';
// Bỏ auth_check.php để khách có thể truy cập trang liên hệ

$success = "";
$errors = [];
$form_data = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

// Kiểm tra xem người dùng đã đăng nhập chưa
$is_logged_in = false;
$current_email = '';
$current_name = '';

if (isset($_SESSION['user']) || isset($_COOKIE['stored_email'])) {
    $is_logged_in = true;
    $current_email = $_SESSION['user'] ?? $_COOKIE['stored_email'];
    $current_name = $_SESSION['username'] ?? $_COOKIE['stored_fullname'] ?? 'Người dùng';
} elseif (isset($_SESSION['admin_logged_in'])) {
    $is_logged_in = true;
    $current_email = $_SESSION['admin_email'];
    $current_name = $_SESSION['admin_user'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST["subject"] ?? '');
    $message = trim($_POST["message"] ?? '');

    if ($is_logged_in) {
        $name = $current_name;
        $email = $current_email;
    } else {
        $name = trim($_POST["name"] ?? '');
        $email = trim($_POST["email"] ?? '');

        if (empty($name)) {
            $errors['name'] = __('name_required');
        }
        if (empty($email)) {
            $errors['email'] = __('email_required');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = __('email_invalid');
        }
    }

    if (empty($subject)) {
        $errors['subject'] = __('subject_required');
    }
    if (empty($message)) {
        $errors['message'] = __('message_required');
    }

    if (empty($errors)) {
        $name_db = mysqli_real_escape_string($conn, $name);
        $email_db = mysqli_real_escape_string($conn, $email);
        $subject_db = mysqli_real_escape_string($conn, $subject);
        $message_db = mysqli_real_escape_string($conn, $message);

        $sql = "INSERT INTO contacts (fullname, email, subject, message) 
                VALUES ('$name_db', '$email_db', '$subject_db', '$message_db')";

        if (mysqli_query($conn, $sql)) {
            $form_data['subject'] = $form_data['message'] = ''; 
            if (!$is_logged_in) {
                $form_data['name'] = $form_data['email'] = '';
            }
        } else {
            $errors['db'] = __('db_error') . mysqli_error($conn);
        }
    } else {
        if (!$is_logged_in) {
            $form_data['name'] = $name;
            $form_data['email'] = $email;
        }
        $form_data['subject'] = $subject;
        $form_data['message'] = $message;
    }
}

$page_title = __('contact_title');
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/contact.css">

<div class="container" style="margin-top: 50px;">
    <h2><?php echo __('contact_title'); ?></h2>

    <?php if ($success != "")
        echo "<div class='success'>$success</div>"; ?>

    <?php if (isset($errors['db'])): ?>
        <div class="error" style="color:red; margin-bottom:15px;"><?php echo $errors['db']; ?></div><?php endif; ?>

    <form method="POST" novalidate>
        <?php echo csrf_tag(); ?>
        
        <?php if (!$is_logged_in): ?>
            <div style="margin-bottom: 20px;">
                <input type="text" name="name" value="<?php echo htmlspecialchars($form_data['name']); ?>"
                    placeholder="<?php echo __('name_placeholder'); ?>" required style="width: 100%; margin-bottom: 2px;">
                <?php if (isset($errors['name'])): ?>
                    <div style="color:red; font-size:13px; text-align:left;"><?php echo $errors['name']; ?></div><?php endif; ?>
            </div>

            <div style="margin-bottom: 20px;">
                <input type="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>"
                    placeholder="<?php echo __('email_placeholder'); ?>" required style="width: 100%; margin-bottom: 2px;">
                <?php if (isset($errors['email'])): ?>
                    <div style="color:red; font-size:13px; text-align:left;"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 20px; font-size: 14px; color: #fff; background: rgba(0,0,0,0.4); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2);">
                <?php echo htmlspecialchars($current_email); ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 20px;">
            <input type="text" name="subject" value="<?php echo htmlspecialchars($form_data['subject']); ?>"
                placeholder="<?php echo __('subject_placeholder'); ?>" required style="width: 100%; margin-bottom: 2px;">
            <?php if (isset($errors['subject'])): ?>
                <div style="color:red; font-size:13px; text-align:left;"><?php echo $errors['subject']; ?></div>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <textarea name="message" rows="5" placeholder="<?php echo __('message_placeholder'); ?>" required
                style="width: 100%; margin-bottom: 2px;"><?php echo htmlspecialchars($form_data['message']); ?></textarea>
            <?php if (isset($errors['message'])): ?>
                <div style="color:red; font-size:13px; text-align:left;"><?php echo $errors['message']; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit"><?php echo __('send_message'); ?></button>
    </form>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
</body>

</html>