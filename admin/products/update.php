<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . "/../../includes/auth_check.php";
restrictToAdmin();

if (!isset($_GET['id'])) {
    header("Location: " . $base_url . "admin/products");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$res = mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'");
$product = mysqli_fetch_assoc($res);

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? '');
    $price_raw = trim($_POST["price"] ?? '');
    $price = str_replace('.', '', $price_raw);
    $description = trim($_POST["description"] ?? '');
    $category_id = isset($_POST["category_id"]) && $_POST["category_id"] !== "" ? intval($_POST["category_id"]) : "NULL";
    $stock_quantity = isset($_POST["stock_quantity"]) ? trim($_POST["stock_quantity"]) : '0';
    $status = isset($_POST["status"]) ? $_POST["status"] : 'hiện';

    if (empty($name))
        $errors['name'] = "Vui lòng nhập tên sản phẩm.";
    if ($price_raw === '')
        $errors['price'] = "Vui lòng nhập giá sản phẩm.";
    if ($stock_quantity === '')
        $errors['stock_quantity'] = "Vui lòng nhập số lượng kho.";

    if (empty($errors)) {
        $name_db = mysqli_real_escape_string($conn, $name);
        $desc_db = mysqli_real_escape_string($conn, $description);
        $status_db = mysqli_real_escape_string($conn, $status);
        $price_db = floatval($price);
        $stock_db = intval($stock_quantity);

        $update_image_sql = "";
        if (!empty($_FILES['image']['name'])) {
            $image_name = $_FILES['image']['name'];
            $target = __DIR__ . "/../../public/assets/products_img/" . basename($image_name);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_db = mysqli_real_escape_string($conn, $image_name);
                $update_image_sql = ", image_id='$image_db'";
            }
        }

        // Xoá ảnh phụ được đánh dấu
        if (!empty($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $del_id) {
                $del_id = intval($del_id);
                mysqli_query($conn, "DELETE FROM product_images WHERE id=$del_id AND product_id='$id'");
            }
        }

        // Thêm ảnh phụ mới
        if (!empty($_FILES['extra_images']['name'][0])) {
            foreach ($_FILES['extra_images']['name'] as $k => $extra_name) {
                if ($extra_name === '') continue;
                $extra_tmp = $_FILES['extra_images']['tmp_name'][$k];
                $extra_target = __DIR__ . "/../../public/assets/products_img/" . basename($extra_name);
                if (move_uploaded_file($extra_tmp, $extra_target)) {
                    $extra_db = mysqli_real_escape_string($conn, $extra_name);
                    mysqli_query($conn, "INSERT INTO product_images (product_id, image_path) VALUES ('$id', '$extra_db')");
                }
            }
        }

        $category_db = ($category_id === "NULL") ? "NULL" : "'$category_id'";
        $sql = "UPDATE products SET category_id=$category_db, name='$name_db', price='$price_db', stock_quantity='$stock_db', description='$desc_db', status='$status_db' $update_image_sql WHERE id='$id'";

        if (mysqli_query($conn, $sql)) {
            header("Location: " . $base_url . "admin/products");
            exit();
        } else {
            $errors['db'] = "Lỗi CSDL: " . mysqli_error($conn);
        }
    }

    $product['name'] = $name;
    $product['price'] = $price;
    $product['stock_quantity'] = $stock_quantity;
    $product['description'] = $description;
    $product['category_id'] = $category_id;
    $product['status'] = $status;
}

// Lấy ảnh phụ hiện tại
$res_extras = mysqli_query($conn, "SELECT id, image_path FROM product_images WHERE product_id='$id' ORDER BY id ASC");
$existing_extras = [];
while ($row = mysqli_fetch_assoc($res_extras)) {
    $existing_extras[] = $row;
}
?>
<?php
$page_title = "Cập Nhật Sản Phẩm";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/update.css">


<div class="form-page">

    <div class="form-page-header">
        <a href="<?php echo $base_url; ?>admin/products" class="back-btn">Quay lại</a>
        <div>
            <h1 class="form-page-title">Chỉnh Sửa Sản Phẩm</h1>
            <p class="form-page-sub">Cập nhật thông tin : <strong><?php echo htmlspecialchars($product['name']); ?></strong></p>
        </div>
    </div>

    <?php if (isset($errors['db'])): ?>
    <div class="alert-error"><?php echo $errors['db']; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate class="form-layout" id="updateForm">
        <?php echo csrf_tag(); ?>

        <!-- LEFT: main fields -->
        <div class="form-left">

            <div class="form-card">
                <h3 class="card-heading">Thông tin cơ bản</h3>

                <div class="form-group">
                    <label>Danh mục</label>
                    <?php
                    require_once __DIR__ . '/../../includes/db_product.php';
                    $categories = getCategories($conn);
                    ?>
                    <select name="category_id" class="form-select">
                        <option value="" <?php echo empty($product['category_id']) ? 'selected' : ''; ?>>— Không phân loại —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tên Sản Phẩm <span class="req">*</span></label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    <?php if (isset($errors['name'])): ?>
                    <span class="field-error"><?php echo $errors['name']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Giá (VNĐ) <span class="req">*</span></label>
                        <div class="input-with-unit">
                            <input type="text" name="price"
                                value="<?php echo ($product['price'] !== '' && $product['price'] !== null) ? number_format((float)$product['price'],0,'','.') : ''; ?>"
                                oninput="this.value=this.value.replace(/\D/g,'').replace(/\B(?=(\d{3})+(?!\d))/g,'.');"
                                required>
                            <span class="unit">VNĐ</span>
                        </div>
                        <?php if (isset($errors['price'])): ?>
                        <span class="field-error"><?php echo $errors['price']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Số lượng kho <span class="req">*</span></label>
                        <input type="number" name="stock_quantity"
                               value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" min="0" required>
                        <?php if (isset($errors['stock_quantity'])): ?>
                        <span class="field-error"><?php echo $errors['stock_quantity']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Trạng thái hiển thị</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="status" value="hiện" <?php echo $product['status']=='hiện'?'checked':''; ?>>
                            <span class="radio-dot on"></span> Hiện (Active)
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="status" value="ẩn" <?php echo $product['status']=='ẩn'?'checked':''; ?>>
                            <span class="radio-dot off"></span> Ẩn (Hidden)
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h3 class="card-heading">Mô tả sản phẩm</h3>
                <div class="form-group">
                    <textarea name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn-cancel" onclick="history.back()">Hủy bỏ</a>
                <button type="submit" class="btn-submit">Lưu</button>
            </div>
        </div>

        <!-- RIGHT: image -->
        <div class="form-right">

            <!-- Ảnh bìa chính -->
            <div class="form-card upload-card">
                <h3 class="card-heading">Ảnh bìa chính</h3>
                <div class="upload-zone upload-zone-trigger" data-input="imgInput">
                    <input type="file" name="image" id="imgInput" class="preview-input"
                           data-target="#preview" accept="image/*" style="display:none;">
                    <img id="preview"
                        src="<?php echo $base_url; ?>public/assets/products_img/<?php echo htmlspecialchars($product['image_id']); ?>"
                        style="max-width:100%; max-height:260px; border-radius:10px; object-fit:contain; display:block;">
                </div>
                <p class="upload-tip">Nhấn để thay đổi</p>
            </div>

            <!-- Ảnh phụ hiện có -->
            <div class="form-card upload-card" style="margin-top:16px;">
                <h3 class="card-heading">Ảnh phụ</h3>

                <?php if (!empty($existing_extras)): ?>
                <p style="font-size:12px; color:#888; margin:0 0 8px 0;">Nhấn <b>✕</b> để xóa(sau khi lưu)</p>
                <div class="extra-img-grid" id="existingExtraGrid">
                    <?php foreach ($existing_extras as $ex): ?>
                    <div class="extra-img-item" id="exItem<?php echo $ex['id']; ?>">
                        <img src="<?php echo $base_url; ?>public/assets/products_img/<?php echo htmlspecialchars($ex['image_path']); ?>"
                             alt="Ảnh phụ">
                        <!-- Hidden checkbox, được check khi nhấn nút xóa -->
                        <input type="checkbox" name="delete_images[]"
                               value="<?php echo $ex['id']; ?>"
                               id="delChk<?php echo $ex['id']; ?>"
                               style="display:none;">
                        <button type="button" class="del-btn"
                                onclick="toggleDeleteExtra(<?php echo $ex['id']; ?>)">&#10005;</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p style="font-size:12px; color:#aaa; margin:0 0 10px 0;">Chưa có ảnh phụ.</p>
                <?php endif; ?>

                <!-- Thêm ảnh phụ mới -->
                <div class="upload-zone extra-zone" id="extraUploadZone"
                     style="cursor:pointer; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:6px;"
                     onclick="document.getElementById('extraImgsInput').click()">
                    <input type="file" name="extra_images[]" id="extraImgsInput"
                           accept="image/*" multiple style="display:none;">
                    <div id="extraPlaceholder" style="text-align:center; color:#aaa;">
                        <div style="font-size:22px;">&#43;</div>
                        <span style="font-size:12px;">Thêm ảnh phụ mới</span>
                    </div>
                    <div id="extraPreviewList" style="display:flex; flex-wrap:wrap; gap:6px; padding:4px;"></div>
                </div>
                <p class="upload-tip" style="font-size:11px; color:#888; margin-top:6px;">Ctrl+Click để chọn nhiều file</p>
            </div>

        </div>

    </form>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/image_preview.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/product_update.js?v=<?php echo time(); ?>"></script>
</body>
</html>
