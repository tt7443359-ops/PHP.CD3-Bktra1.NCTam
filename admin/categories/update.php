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

$edit_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($edit_id <= 0) {
    header("Location: " . $base_url . "admin/categories");
    exit();
}

$res = mysqli_query($conn, "SELECT * FROM categories WHERE id = $edit_id");
if (mysqli_num_rows($res) == 0) {
    header("Location: " . $base_url . "admin/categories");
    exit();
}
$edit_cat = mysqli_fetch_assoc($res);

$errors = [];
$form_data = ['name' => $edit_cat['name'], 'description' => $edit_cat['description']];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $errors['name'] = "Vui lòng nhập tên danh mục.";
    } else {
        $nm_db = mysqli_real_escape_string($conn, $name);
        $check_sql = "SELECT id FROM categories WHERE name = '$nm_db' AND id != $edit_id";
        $check_res = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_res) > 0) {
            $errors['name'] = "Tên danh mục này đã tồn tại, vui lòng chọn tên khác.";
        }
    }

    if (empty($errors)) {
        $nm_db = mysqli_real_escape_string($conn, $name);
        $ds_db = mysqli_real_escape_string($conn, $description);

        $sql = "UPDATE categories SET name='$nm_db', description='$ds_db' WHERE id=$edit_id";

        if (mysqli_query($conn, $sql)) {
            header("Location: " . $base_url . "admin/categories");
            exit();
        } else {
            $error_msg = "Lỗi CSDL: " . mysqli_error($conn);
        }
    } else {
        $form_data['name'] = $name;
        $form_data['description'] = $description;
    }
}
?>

<?php
$page_title = "Sửa Danh Mục";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/create.css">

<div class="main-content">
    <div class="form-card">
        <h2>Sửa Danh Mục</h2>
        <?php if (isset($error_msg)) echo "<p style='color:red;'>$error_msg</p>"; ?>
        <form method="POST" action="<?php echo $base_url; ?>admin/update-category/<?php echo $edit_id; ?>" novalidate>
            <?php echo csrf_tag(); ?>
            <div class="form-group">
                <label>Tên danh mục</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required>
                <?php if (isset($errors['name'])): ?><span style="color:red; font-size:13px; display:block; margin-top:5px;"><?php echo $errors['name']; ?></span><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" rows="3"><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn-submit" style="width: 100%; margin-top: 15px;">Lưu</button>
        </form>
        <a href="<?php echo $base_url; ?>admin/categories" class="back-link" style="display: block; text-align: center; margin-top: 15px;">Hủy bỏ thay đổi</a>
    </div>
</div>
</body>
</html>
