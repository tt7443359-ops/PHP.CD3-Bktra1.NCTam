<?php
require_once __DIR__ . '/../../includes/db_product.php';

// Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn lấy sản phẩm
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = $id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: " . $base_url . "shop");
    exit();
}

// Lấy ảnh phụ từ bảng product_images
$sql_imgs = "SELECT id, image_path FROM product_images WHERE product_id = $id ORDER BY id ASC";
$result_imgs = mysqli_query($conn, $sql_imgs);
$extra_images = [];
while ($row = mysqli_fetch_assoc($result_imgs)) {
    $extra_images[] = $row['image_path'];
}
// Gộp: ảnh chính trước, ảnh phụ sau
$all_images = array_merge([$product['image_id']], $extra_images);
?>

<?php
$page_title = __d($product, 'name') . " - " . __('product_details');
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/details.css">


<div style="max-width: 1200px; margin: 30px auto 0; padding: 0 20px;">

</div>

<div class="detail-container" style="margin-top: 10px;">

    <!-- CỘT TRÁI: ẢNH, LENS ZOOM VÀ THUMBNAIL STRIP -->
    <div class="col-left">
        <div class="img-zoom-container">
            <img id="myimage"
                src="<?php echo $base_url; ?>public/assets/products_img/<?php echo htmlspecialchars($product['image_id']); ?>"
                alt="cover"
                onclick="openImgModal(this.src)"
                style="cursor:zoom-in;">
            <div id="mylens" class="img-zoom-lens"></div>
        </div>

        <?php if (count($all_images) > 1): ?>
        <div class="thumb-strip" id="thumbStrip">
            <?php foreach ($all_images as $i => $img_path): ?>
            <div class="thumb-item <?php echo $i === 0 ? 'active' : ''; ?>"
                 data-src="<?php echo $base_url; ?>public/assets/products_img/<?php echo htmlspecialchars($img_path); ?>">
                <img src="<?php echo $base_url; ?>public/assets/products_img/<?php echo htmlspecialchars($img_path); ?>"
                     alt="<?php echo __('image_alt'); ?> <?php echo $i + 1; ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Read Sample button -->
        <button class="btn-read-sample" onclick="openReadSample()"><?php echo __('read_sample'); ?></button>
    </div>


    <!-- ── LIGHTBOX MODAL ─────────────────────────── -->
    <div class="img-modal-overlay" id="imgModalOverlay" onclick="closeImgModal(event)">
        <div class="img-modal">
            <button class="img-modal-close" onclick="closeImgModalDirect()">&#10005;</button>

            <!-- Ảnh lớn -->
            <div class="img-modal-img-wrap">
                <img id="modalMainImg"
                     src="<?php echo $base_url; ?>public/assets/products_img/<?php echo htmlspecialchars($product['image_id']); ?>"
                     alt="cover">
            </div>

            <!-- Tên + thumbs -->
            <div class="img-modal-info">
                <p class="img-modal-title"><?php echo htmlspecialchars(__d($product, 'name')); ?></p>

                <?php if (count($all_images) > 1): ?>
                <div class="img-modal-thumbs">
                    <?php foreach ($all_images as $i => $img_path): ?>
                    <div class="thumb-item <?php echo $i === 0 ? 'active' : ''; ?>"
                         id="modalThumb<?php echo $i; ?>"
                         data-src="<?php echo $base_url; ?>public/assets/products_img/<?php echo htmlspecialchars($img_path); ?>"
                         onclick="switchModalImg(this, <?php echo $i; ?>)">
                        <img src="<?php echo $base_url; ?>public/assets/products_img/<?php echo htmlspecialchars($img_path); ?>"
                             alt="<?php echo __('image_alt'); ?> <?php echo $i + 1; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── READ SAMPLE MODAL (rỗng, chỉ UI) ─────── -->
    <div class="img-modal-overlay" id="readSampleOverlay" onclick="closeReadSample(event)">
        <div class="img-modal" style="max-width:560px; flex-direction:column; padding:32px 28px; min-height:auto;">
            <button class="img-modal-close" onclick="closeReadSampleDirect()">&#10005;</button>
            <p style="font-size:15px; font-weight:600; color:#0f1111; margin:0 0 10px 0;">
                <?php echo htmlspecialchars(__d($product, 'name')); ?>
            </p>
            <p style="font-size:13px; color:#888; margin:0;">
                <?php echo __('sample_unavailable'); ?>
            </p>
        </div>
    </div>

    <!-- CỘT GIỮA: THÔNG TIN VÀ KHUNG RESULT ZOOM -->
    <div class="col-middle">
        <!-- Khung chiếu Zoom ẩn ban đầu -->
        <div id="myresult" class="img-zoom-result"></div>

        <h1><?php echo htmlspecialchars(__d($product, 'name')); ?></h1>
        <span class="amz-author">
            <?php echo htmlspecialchars($product['category_name'] ? __($product['category_name']) : __('not_updated_yet')); ?>
            (<?php echo __('author'); ?>)
        </span>
        <div class="desc-title"><?php echo __('story_plot'); ?></div>
        <div class="desc-wrapper" id="descWrapper">
            <p class="desc" id="descText"><?php echo nl2br(htmlspecialchars(__d($product, 'description'))); ?></p>
            <div class="desc-fade" id="descFade"></div>
        </div>
        <button class="desc-toggle" id="descToggle" style="display:none;" 
                data-text-more="<?php echo __('view_more_down'); ?>" 
                data-text-less="<?php echo __('collapse_up'); ?>"
                onclick="toggleDesc()"><?php echo __('view_more_down'); ?></button>

    </div>

    <!-- CỘT PHẢI: KHUNG ĐẶT HÀNG -->
    <div class="col-right">
        <div class="buy-box">
            <div class="price"><?php echo __p($product['price']); ?></div>

            <?php if ($product['stock_quantity'] > 0): ?>
                <div class="stock-info"><?php echo __('in_stock'); ?></div>
            <?php else: ?>
                <div class="stock-info low-stock"><?php echo __('out_of_stock'); ?></div>
            <?php endif; ?>

            <p style="font-size: 13px; color: #555; margin-bottom: 20px;">
                <?php echo __('returns_policy'); ?><br>
                <?php echo __('stock_quantity_label'); ?> <b><?php echo $product['stock_quantity']; ?></b>
            </p>

            <div class="buy-actions">
                <?php if (!isset($_SESSION['admin_logged_in'])): ?>
                    <?php if (isset($_SESSION['user']) || isset($_COOKIE['stored_email'])): ?>
                        <a href="<?php echo $base_url; ?>actions/add_to_cart.php?id=<?php echo $product['id']; ?>"
                            class="btn-buy yellow"><?php echo __('add_to_cart'); ?></a>
                        <a href="<?php echo $base_url; ?>actions/add_to_cart.php?id=<?php echo $product['id']; ?>&redirect=cart"
                            class="btn-buy orange"><?php echo __('buy_now'); ?></a>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>login?action=add_to_cart&id=<?php echo $product['id']; ?>" class="btn-buy yellow"><?php echo __('add_to_cart'); ?></a>
                        <a href="<?php echo $base_url; ?>login?action=buy_now&id=<?php echo $product['id']; ?>" class="btn-buy orange"><?php echo __('place_order_btn'); ?></a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn-buy yellow" disabled style="opacity:0.5; cursor:not-allowed;">Admin mode</button>
                    <a href="<?php echo $base_url; ?>admin/update-product/<?php echo $product['id']; ?>"
                        class="btn-buy orange"><?php echo __('edit_product'); ?></a>
                <?php endif; ?>
            </div>

            <div style="margin-top:20px; text-align:center; font-size:12px; color:#007185;">
                <span style="cursor:pointer;">
                <?php echo __('untrusted'); ?></span><br>
                <?php echo __('fulfilled_by'); ?><br>
                Novel2x<br>
                <?php echo __('sold_by'); ?><br>
                NCTam<br>
                <?php echo __('payment'); ?><br>
                <?php echo __('secure_transaction'); ?><br>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/shop.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/details.js"></script>
<?php require_once __DIR__ . "/../../includes/footer.php"; ?>
</body>

</html>
