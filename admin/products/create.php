<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . "/../../includes/auth_check.php";
restrictToAdmin();

$errors = [];
$form_data = ['name' => '', 'price' => '', 'stock_quantity' => '0', 'description' => '', 'category_id' => 1, 'status' => 'hiện'];

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
    if (empty($_FILES['image']['name']))
        $errors['image'] = "Vui lòng chọn ảnh sản phẩm.";

    if (empty($errors)) {
        $name_db = mysqli_real_escape_string($conn, $name);
        $desc_db = mysqli_real_escape_string($conn, $description);
        $status_db = mysqli_real_escape_string($conn, $status);
        $price_db = floatval($price);
        $stock_db = intval($stock_quantity);
        $image_name = $_FILES['image']['name'];
        $target = __DIR__ . "/../../public/assets/products_img/" . basename($image_name);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_db = mysqli_real_escape_string($conn, $image_name);
            $category_db = ($category_id === "NULL") ? "NULL" : "'$category_id'";
            $sql = "INSERT INTO products (category_id, name, price, stock_quantity, description, image_id, status) 
                    VALUES ($category_db, '$name_db', '$price_db', '$stock_db', '$desc_db', '$image_db', '$status_db')";

            if (mysqli_query($conn, $sql)) {
                $new_id = mysqli_insert_id($conn);

                // Lưu ảnh phụ vào bảng product_images
                if (!empty($_FILES['extra_images']['name'][0])) {
                    foreach ($_FILES['extra_images']['name'] as $k => $extra_name) {
                        if ($extra_name === '') continue;
                        $extra_tmp = $_FILES['extra_images']['tmp_name'][$k];
                        $extra_target = __DIR__ . "/../../public/assets/products_img/" . basename($extra_name);
                        if (move_uploaded_file($extra_tmp, $extra_target)) {
                            $extra_db = mysqli_real_escape_string($conn, $extra_name);
                            mysqli_query($conn, "INSERT INTO product_images (product_id, image_path) VALUES ('$new_id', '$extra_db')");
                        }
                    }
                }

                header("Location: " . $base_url . "admin/products");
                exit();
            } else {
                $errors['db'] = "Lỗi CSDL: " . mysqli_error($conn);
            }
        } else {
            $errors['image'] = "Lỗi: Không thể tải ảnh lên!";
        }
    }

    $form_data = [
        'name' => $name,
        'price' => $price,
        'stock_quantity' => $stock_quantity,
        'description' => $description,
        'category_id' => $category_id,
        'status' => $status
    ];
}
?>
<?php
$page_title = "Thêm Sản Phẩm Mới";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/create.css">

<div class="form-page">

    <!-- Page header -->
    <div class="form-page-header">
        <a href="<?php echo $base_url; ?>admin/products" class="back-btn">&#8592; Quay lại</a>
        <div>
            <h1 class="form-page-title">Thêm Sản Phẩm Mới</h1>
        </div>
    </div>

    <?php if (isset($errors['db'])): ?>
    <div class="alert-error"><?php echo $errors['db']; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate class="form-layout">
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
                        <option value="" <?php echo ($form_data['category_id'] === "NULL" || empty($form_data['category_id'])) ? 'selected' : ''; ?>>— Không phân loại —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $form_data['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tên Sản Phẩm <span class="req">*</span></label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($form_data['name']); ?>"
                           placeholder="Nhập tên sản phẩm" required>
                    <?php if (isset($errors['name'])): ?>
                    <span class="field-error"><?php echo $errors['name']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Giá (VNĐ) <span class="req">*</span></label>
                        <div class="input-with-unit">
                            <input type="text" name="price"
                                value="<?php echo $form_data['price'] !== '' ? number_format((float)$form_data['price'],0,'','.') : ''; ?>"
                                placeholder="0"
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
                               value="<?php echo htmlspecialchars($form_data['stock_quantity']); ?>" min="0" required>
                        <?php if (isset($errors['stock_quantity'])): ?>
                        <span class="field-error"><?php echo $errors['stock_quantity']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Trạng thái hiển thị</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="status" value="hiện" <?php echo $form_data['status']=='hiện'?'checked':''; ?>>
                            <span class="radio-dot on"></span> Hiện (Active)
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="status" value="ẩn" <?php echo $form_data['status']=='ẩn'?'checked':''; ?>>
                            <span class="radio-dot off"></span> Ẩn (Hidden)
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h3 class="card-heading">Mô tả sản phẩm</h3>
                <div class="form-group">
                    <textarea name="description" rows="5"
                        placeholder="Nhập nội dung giới thiệu ..."><?php echo htmlspecialchars($form_data['description']); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?php echo $base_url; ?>admin/products" class="btn-cancel">Hủy bỏ</a>
                <button type="submit" class="btn-submit">&#43; Thêm</button>
            </div>
        </div>

        <!-- RIGHT: image upload -->
        <div class="form-right">

            <!-- Ảnh bìa chính -->
            <div class="form-card upload-card">
                <h3 class="card-heading">Ảnh bìa chính <span class="req">+</span></h3>
                <div class="upload-zone upload-zone-trigger" data-input="imgInput">
                    <input type="file" name="image" id="imgInput" class="preview-input"
                           data-target="#preview" data-placeholder="#upload-placeholder"
                           accept="image/*" required style="display:none;">
                    <div id="upload-placeholder" class="upload-placeholder">
                        <div class="upload-icon">&#128444;</div>
                        <span class="upload-hint">PNG, JPG, WEBP</span>
                    </div>
                    <img id="preview" src="#" style="display:none; max-width:100%; max-height:260px; border-radius:10px; object-fit:contain;">
                </div>
                <?php if (isset($errors['image'])): ?>
                <span class="field-error"><?php echo $errors['image']; ?></span>
                <?php endif; ?>
            </div>

            <!-- Ảnh phụ (nhiều ảnh) -->
            <div class="form-card upload-card" style="margin-top:16px;">
                <h3 class="card-heading">Ảnh phụ <span style="font-weight:400; font-size:12px; color:#888;">(tuỳ chọn, nhiều ảnh)</span></h3>
                <div class="upload-zone extra-zone" id="extraUploadZone" style="cursor:pointer; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:6px;"
                     onclick="document.getElementById('extraImgsInput').click()">
                    <input type="file" name="extra_images[]" id="extraImgsInput"
                           accept="image/*" multiple style="display:none;">
                    <div id="extraPlaceholder" style="text-align:center; color:#aaa;">
                        <div style="font-size:26px;">&#43;</div>
                        <span style="font-size:12px;">Thêm ảnh phụ (PNG, JPG, WEBP)</span>
                    </div>
                    <!-- Preview thumbnails hiện ra ở đây -->
                    <div id="extraPreviewList" style="display:flex; flex-wrap:wrap; gap:6px; padding:4px;"></div>
                </div>
                <p class="upload-tip" style="font-size:11px; color:#888; margin-top:6px;">Chọn nhiều file cùng lúc bằng Ctrl+Click</p>
            </div>

        </div>

    </form>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/image_preview.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/product_create.js?v=<?php echo time(); ?>"></script>
</body>
</html>
