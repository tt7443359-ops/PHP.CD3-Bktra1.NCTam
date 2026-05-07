<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/db_product.php';
require_once __DIR__ . "/../../includes/auth_check.php";

// khách
if (!isset($_SESSION['user'])) {
    header("Location: " . $base_url . "login");
    exit();
}

// Cập nhật số lượng giỏ hàng
if (isset($_GET['action']) && $_GET['action'] == 'update_qty' && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $q) {
        $q = intval($q);
        $res = mysqli_query($conn, "SELECT stock_quantity FROM products WHERE id = " . intval($id));
        if ($row = mysqli_fetch_assoc($res)) {
            $stock = $row['stock_quantity'];
            if ($q > $stock)
                $q = $stock;
            if ($q < 1)
                $q = 1;
            $_SESSION['cart'][$id] = $q;
        }
    }
    header("Location: " . $base_url . "cart?action=updated");
    exit();
}

// Bỏ chọn sản phẩm
if (isset($_POST['delete_selected']) && isset($_POST['remove_ids'])) {
    foreach ($_POST['remove_ids'] as $id_to_remove) {
        unset($_SESSION['cart'][$id_to_remove]);
    }
    header("Location: " . $base_url . "cart");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<div style='text-align:center; padding:50px; font-family:Poppins;'>
            <h2>Giỏ hàng trống</h2>
            <a href='" . $base_url . "shop' style='color:#007bff; text-decoration:none;'>ủng hộ Admin...</a>
          </div>";
    exit();
}
?>

<?php
$page_title = "Giỏ Hàng Của Bạn";
require_once __DIR__ . "/../../includes/header.php";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/view_cart.css?v=<?php echo time(); ?>">


<div class="cart-wrapper">
    <div class="cart-header">
        <div class="cart-header-checkbox"><input type="checkbox" id="check-all-header" onclick="toggleAll(this)"></div>
        <div class="cart-header-product">Sản Phẩm</div>
        <div class="cart-header-price">Đơn Giá</div>
        <div class="cart-header-qty">Số Lượng</div>
        <div class="cart-header-total">Số Tiền</div>
        <div class="cart-header-action">Thao Tác</div>
    </div>

    <form method="POST" action="<?php echo $base_url; ?>cart" id="cart-form">
        <?php echo csrf_tag(); ?>
        <?php
        $grand_total = 0;
        foreach ($_SESSION['cart'] as $id => $qty):
            $res = mysqli_query($conn, "SELECT * FROM products WHERE id = " . intval($id));
            $p = mysqli_fetch_assoc($res);

            if (!$p) {
                unset($_SESSION['cart'][$id]);
                continue;
            }

            $subtotal = $p['price'] * $qty;
            $grand_total += $subtotal;
            $image_url = !empty($p['image_id']) ? $base_url . 'public/assets/products_img/' . htmlspecialchars($p['image_id']) : $base_url . 'public/assets/images/default-product.png';
        ?>
            <div class="cart-item">
                <div class="cart-item-checkbox"><input type="checkbox" name="remove_ids[]" value="<?php echo $id; ?>" class="item-checkbox" id="remove_<?php echo $id; ?>" data-price="<?php echo $p['price']; ?>" data-qty="<?php echo $qty; ?>" onclick="syncCheckboxes()"></div>
                <div class="cart-item-product">
                    <a href="<?php echo $base_url; ?>shop/details/<?php echo $id; ?>" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 15px;">
                        <img src="<?php echo $image_url; ?>" alt="" class="cart-item-img">
                        <span class="cart-item-name"><?php echo htmlspecialchars($p['name']); ?></span>
                    </a>
                </div>
                <div class="cart-item-price">₫<?php echo number_format($p['price'], 0, ',', '.'); ?></div>
                <div class="cart-item-qty">
                    <div class="qty-control">
                        <button type="button" class="qty-btn" onclick="if(this.nextElementSibling.value > 1) { this.nextElementSibling.stepDown(); document.getElementById('cart-form').action='<?php echo $base_url; ?>cart?action=update_qty'; document.getElementById('cart-form').submit(); }">-</button>
                        <input type="number" class="qty-input" name="qty[<?php echo $id; ?>]" value="<?php echo $qty; ?>" min="1" max="<?php echo $p['stock_quantity']; ?>" onchange="document.getElementById('cart-form').action='<?php echo $base_url; ?>cart?action=update_qty'; document.getElementById('cart-form').submit();">
                        <button type="button" class="qty-btn" onclick="if(this.previousElementSibling.value < <?php echo $p['stock_quantity']; ?>) { this.previousElementSibling.stepUp(); document.getElementById('cart-form').action='<?php echo $base_url; ?>cart?action=update_qty'; document.getElementById('cart-form').submit(); }">+</button>
                    </div>
                    <div class="stock-info">Còn: <?php echo $p['stock_quantity']; ?></div>
                </div>
                <div class="cart-item-total">₫<?php echo number_format($subtotal, 0, ',', '.'); ?></div>
                <div class="cart-item-action">
                    <button type="submit" name="delete_selected" value="1" class="btn-delete-item" onclick="document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false); document.getElementById('remove_<?php echo $id; ?>').checked = true; return confirm('Bạn có chắc muốn bỏ sản phẩm này?');">Xóa</button>
                </div>
            </div>
        <?php endforeach; ?>
        
        <noscript>
            <button type="submit" formaction="<?php echo $base_url; ?>cart?action=update_qty" style="margin-bottom:15px; padding: 10px; background: #007bff; color: white; border: none;">Cập nhật giỏ hàng</button>
        </noscript>

    <div class="cart-footer">
        <div class="cart-footer-left">
            <input type="checkbox" id="check-all-footer" onclick="toggleAll(this)">
            <label for="check-all-footer" style="font-size: 14px; cursor: pointer; user-select: none;">Tổng SP (<?php echo count($_SESSION['cart']); ?>)</label>
            <button type="submit" name="delete_selected" class="btn-delete-selected" onclick="return confirm('Bạn có chắc muốn xóa các sản phẩm đã chọn?')">Xóa</button>
        </div>
        <div class="cart-footer-right">
            <div class="total-label">Tổng thanh toán (<span id="selected-count">0</span> Sản phẩm):</div>
            <div class="total-price">₫<span id="selected-price">0</span></div>
            <button type="button" class="btn-checkout" onclick="checkoutSelected()" style="border: none; font-size: 16px; cursor: pointer;">Mua Hàng</button>
        </div>
    </div>
    </form>
</div>

<!-- Modal Empty Selection -->
<div id="emptySelectModal" class="store-modal">
    <div class="store-modal-content" style="width: 350px;">
        <div class="modal-body" style="padding: 40px 20px; font-size: 16px; color: #333; text-align: center;">
            Bạn vẫn chưa chọn sản phẩm nào để mua.
        </div>
        <div class="modal-footer" style="padding: 0; border-top: 1px solid #eee;">
            <button type="button" style="width: 100%; padding: 12px; border: none; background: #ee4d2d; color: #fff; font-size: 16px; cursor: pointer;" onclick="document.getElementById('emptySelectModal').classList.remove('show')">OK</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../../includes/footer.php"; ?>
<script>const BASE_URL = '<?php echo $base_url; ?>';</script>
<script src="<?php echo $base_url; ?>public/assets/js/view_cart.js?v=<?php echo time(); ?>"></script>
</body>

</html>