<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/db_product.php';
require_once __DIR__ . "/../../includes/auth_check.php";

// Auto migrate missing columns for checkout
try { mysqli_query($conn, "ALTER TABLE orders ADD COLUMN shipping_address TEXT NULL"); } catch (Exception $e) {}
try { mysqli_query($conn, "ALTER TABLE orders ADD COLUMN shipping_phone VARCHAR(20) NULL"); } catch (Exception $e) {}
try { mysqli_query($conn, "ALTER TABLE orders ADD COLUMN note TEXT NULL"); } catch (Exception $e) {}

// Xác thực: Chỉ Khách hàng được thanh toán
if (!isset($_SESSION['user'])) {
    header("Location: " . $base_url . "login");
    exit();
}

// Tập hợp giỏ hàng
$cart_items   = [];
$grand_total  = 0;
$is_buy_now   = isset($_GET['id']);

$email_user = mysqli_real_escape_string($conn, $_SESSION['user'] ?? $_COOKIE['stored_email'] ?? '');
$user_info = ['phone' => '', 'address' => '', 'fullname' => ''];
$user_id_val = 'NULL';

if (!empty($email_user)) {
    $res_user = mysqli_query($conn, "SELECT id, phone, address, fullname FROM users WHERE email = '$email_user' LIMIT 1");
    if ($res_user && mysqli_num_rows($res_user) > 0) {
        $user_info = mysqli_fetch_assoc($res_user);
        $user_id_val = $user_info['id'];
        if (!empty($user_info['fullname'])) {
            $_SESSION['username'] = $user_info['fullname'];
        }
    }
}

if ($is_buy_now) {
    $id  = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id=$id AND status='hiện'");
    $p   = mysqli_fetch_assoc($res);
    if (!$p) { header("Location: " . $base_url . "shop"); exit(); }
    $cart_items[] = ['product' => $p, 'qty' => 1, 'subtotal' => $p['price']];
    $grand_total  = $p['price'];
} else {
    if (empty($_SESSION['cart'])) { header("Location: " . $base_url . "cart"); exit(); }
    $selected_items = isset($_GET['items']) ? explode(',', $_GET['items']) : array_keys($_SESSION['cart']);
    foreach ($_SESSION['cart'] as $pid => $qty) {
        if (!in_array($pid, $selected_items)) continue;
        $res = mysqli_query($conn, "SELECT * FROM products WHERE id=" . intval($pid));
        $p   = mysqli_fetch_assoc($res);
        if (!$p) continue;
        $sub          = $p['price'] * $qty;
        $cart_items[] = ['product' => $p, 'qty' => $qty, 'subtotal' => $sub];
        $grand_total += $sub;
    }
    if (empty($cart_items)) { header("Location: " . $base_url . "shop"); exit(); }
}

// Lấy thông tin người mua
$buyer_name    = $user_info['fullname'] ?: ($_SESSION['username'] ?? ($_COOKIE['stored_fullname'] ?? ''));
$default_addr  = $user_info['address'] ?? '';

// Xử lý POST (đặt hàng)
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        die("Lỗi bảo mật CSRF.");
    }

    $ship_name    = trim($_POST['ship_name']    ?? '');
    $ship_phone   = trim($_POST['ship_phone']   ?? '');
    $ship_address = trim($_POST['ship_address'] ?? '');
    $ship_note    = trim($_POST['ship_note']    ?? '');

    if (!$ship_name)    $errors[] = "Vui lòng nhập họ tên người nhận.";
    if (!$ship_address) $errors[] = "Vui lòng nhập địa chỉ giao hàng.";

    if (empty($errors)) {
        // Cập nhật ngược lại thông tin hồ sơ (nếu có thay đổi)
        if ($user_id_val != 'NULL') {
            $upd_name = mysqli_real_escape_string($conn, $ship_name);
            $upd_phone = mysqli_real_escape_string($conn, $ship_phone);
            $upd_addr = mysqli_real_escape_string($conn, $ship_address);
            mysqli_query($conn, "UPDATE users SET fullname='$upd_name', phone='$upd_phone', address='$upd_addr' WHERE id=$user_id_val");
            $_SESSION['username'] = $ship_name;
        }

        // Rebuild cart từ POST (mua ngay hoặc giỏ hàng)
        $final_items = [];
        $final_total = 0;
        if ($is_buy_now) {
            $pid = intval($_GET['id']);
            $r   = mysqli_query($conn, "SELECT id, price, stock_quantity FROM products WHERE id=$pid AND status='hiện'");
            $p   = mysqli_fetch_assoc($r);
            if ($p) { $final_items[] = ['id' => $p['id'], 'qty' => 1, 'price' => $p['price']]; $final_total = $p['price']; }
        } else {
            $selected_items = isset($_GET['items']) ? explode(',', $_GET['items']) : array_keys($_SESSION['cart']);
            foreach ($_SESSION['cart'] as $pid => $qty) {
                if (!in_array($pid, $selected_items)) continue;
                $r = mysqli_query($conn, "SELECT id, price, stock_quantity FROM products WHERE id=" . intval($pid));
                $p = mysqli_fetch_assoc($r);
                if (!$p) continue;
                $qty = min($qty, $p['stock_quantity']);
                $final_items[] = ['id' => $p['id'], 'qty' => $qty, 'price' => $p['price']];
                $final_total  += $p['price'] * $qty;
            }
        }

        mysqli_begin_transaction($conn);
        try {
            $note_escaped   = mysqli_real_escape_string($conn, $ship_note);
            $name_escaped   = mysqli_real_escape_string($conn, $ship_name);
            $phone_escaped  = mysqli_real_escape_string($conn, $ship_phone);
            $addr_escaped   = mysqli_real_escape_string($conn, $ship_address);
            $email_escaped  = mysqli_real_escape_string($conn, $email_user ?: 'Khách');

            if ($user_id_val != 'NULL') {
                $sql = "INSERT INTO orders (user_id, fullname_guest, email_guest, total_price, status, shipping_address, shipping_phone, note)
                        VALUES ($user_id_val, '$name_escaped', '$email_escaped', '$final_total', 'chờ xác nhận', '$addr_escaped', '$phone_escaped', '$note_escaped')";
            } else {
                $sql = "INSERT INTO orders (fullname_guest, email_guest, total_price, status, shipping_address, shipping_phone, note)
                        VALUES ('$name_escaped', '$email_escaped', '$final_total', 'chờ xác nhận', '$addr_escaped', '$phone_escaped', '$note_escaped')";
            }
            mysqli_query($conn, $sql);
            $order_id = mysqli_insert_id($conn);

            foreach ($final_items as $item) {
                mysqli_query($conn, "INSERT INTO order_details (order_id, product_id, quantity, price_at_buy)
                    VALUES ($order_id, {$item['id']}, {$item['qty']}, {$item['price']})");
                mysqli_query($conn, "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - {$item['qty']}) WHERE id = {$item['id']}");
            }

            mysqli_commit($conn);
            if (!$is_buy_now) {
                if (isset($_GET['items'])) {
                    foreach (explode(',', $_GET['items']) as $pid) {
                        unset($_SESSION['cart'][$pid]);
                    }
                } else {
                    unset($_SESSION['cart']);
                }
            }
            header("Location: " . $base_url . "order-history");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = "Lỗi CSDL: " . $e->getMessage();
        }
    }
}

$page_title = __('checkout');
require_once __DIR__ . "/../../includes/header.php";
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/map_picker.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/checkout.css">

<div class="store-checkout">
    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="" id="checkoutForm">
        <?php echo csrf_tag(); ?>
        <?php if ($is_buy_now): ?>
            <input type="hidden" name="buy_now_id" value="<?php echo intval($_GET['id']); ?>">
        <?php endif; ?>
        
        <!-- Hidden Inputs for Submission -->
        <input type="hidden" name="ship_name" id="ship_name_input" value="<?php echo htmlspecialchars($buyer_name); ?>">
        <input type="hidden" name="ship_phone" id="ship_phone_input" value="<?php echo htmlspecialchars($user_info['phone'] ?? ''); ?>">
        <input type="hidden" name="ship_address" id="ship_address_input" value="<?php echo htmlspecialchars($default_addr); ?>">

        <!-- Address Section -->
        <div class="checkout-section address-section">
            <div class="section-title"><i class="fa-solid fa-location-dot"></i> <?php echo __('shipping_address'); ?></div>
            <div class="address-content">
                <div class="address-info">
                    <strong><span id="display_name"><?php echo htmlspecialchars($buyer_name ?: __('not_updated_name')); ?></span> (+84) <span id="display_phone"><?php echo htmlspecialchars(ltrim($user_info['phone'] ?? __('not_updated_phone'), '0')); ?></span></strong>
                    <span id="display_address"><?php echo htmlspecialchars($default_addr ?: __('not_updated_address')); ?></span>
                    <span class="default-badge"><?php echo __('default'); ?></span>
                </div>
                <a class="btn-change-address" onclick="openAddressModal()"><?php echo __('change'); ?></a>
            </div>
        </div>
        
        <!-- Products Section -->
        <div class="checkout-section">
            <div class="products-header">
                <div class="col-product"><?php echo __('ordered_products'); ?></div>
                <div class="col-price"><?php echo __('unit_price'); ?></div>
                <div class="col-qty"><?php echo __('quantity'); ?></div>
                <div class="col-subtotal"><?php echo __('subtotal'); ?></div>
            </div>
            
            <div class="shop-name">
                <span class="mall-badge">Mall</span> Novel2x Shop <a href="#" class="chat-btn"><i class="fa-regular fa-message"></i> <?php echo __('chat_now'); ?></a>
            </div>
            
            <?php foreach ($cart_items as $ci): 
                $img = !empty($ci['product']['image_id']) ? $base_url . 'public/assets/products_img/' . htmlspecialchars($ci['product']['image_id']) : $base_url . 'public/assets/images/default-product.png';
            ?>
            <div class="product-item">
                <div class="col-product">
                    <img src="<?php echo $img; ?>" alt="">
                    <span class="product-name"><?php echo htmlspecialchars($ci['product']['name']); ?></span>
                </div>
                <div class="col-price">₫<?php echo number_format($ci['product']['price'], 0, ',', '.'); ?></div>
                <div class="col-qty"><?php echo $ci['qty']; ?></div>
                <div class="col-subtotal">₫<?php echo number_format($ci['subtotal'], 0, ',', '.'); ?></div>
            </div>
            <?php endforeach; ?>
            
            <div class="checkout-note-shipping">
                <div class="note-section">
                    <span style="white-space:nowrap;"><?php echo __('seller_note'); ?></span>
                    <input type="text" name="ship_note" placeholder="<?php echo __('note_placeholder'); ?>">
                </div>
                <div class="shipping-section">
                    <span style="color: #00bfa5;"><?php echo __('shipping_method'); ?> </span>
                    <strong><?php echo __('self_delivery'); ?></strong>
                    <span style="color:#05a; margin-left: 15px; cursor:pointer;" onclick="openShippingModal()"><?php echo __('change'); ?></span>
                    <div style="font-size: 13px; color: #ee4d2d; margin-top: 5px;"><?php echo __('prefer_delivery_time'); ?></div>
                </div>
            </div>
            
            <div class="shop-total">
                <?php echo __('order_total'); ?> (<?php echo count($cart_items); ?> <?php echo __('items_count'); ?>): <span class="total-price">₫<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
            </div>
        </div>
        
        <!-- Footer Section -->
        <div class="checkout-section footer-section">
            <div class="payment-method">
                <div class="payment-header">
                    <div class="payment-title"><?php echo __('payment_method'); ?></div>
                    <div class="payment-summary" id="paymentSummary">
                        <span id="currentPaymentName"><?php echo __('cod'); ?></span>
                        <a href="javascript:void(0)" class="btn-change-payment" onclick="togglePaymentOptions()"><?php echo __('change'); ?></a>
                    </div>
                </div>
                
                <div class="payment-options-wrapper" id="paymentOptionsWrapper" style="display: none;">
                    <input type="hidden" name="payment_method" id="payment_method_input" value="cod">
                    <div class="payment-options">
                        <button type="button" class="btn-payment" onclick="selectPayment(this, 'shopeepay', '<?php echo __('shopeepay'); ?>')"><?php echo __('shopeepay'); ?></button>
                        <button type="button" class="btn-payment" onclick="selectPayment(this, 'card', '<?php echo __('credit_card'); ?>')"><?php echo __('credit_card'); ?></button>
                        <button type="button" class="btn-payment disabled" onclick="return false;">Google Pay</button>
                        <button type="button" class="btn-payment" onclick="selectPayment(this, 'napas', '<?php echo __('napas_card'); ?>')"><?php echo __('napas_card'); ?></button>
                        <button type="button" class="btn-payment active" onclick="selectPayment(this, 'cod', '<?php echo __('cod'); ?>')"><?php echo __('cod'); ?></button>
                        <button type="button" class="btn-payment" onclick="selectPayment(this, 'bank', '<?php echo __('bank_transfer'); ?>')"><?php echo __('bank_transfer'); ?></button>
                    </div>
                </div>
            </div>
            
            <div class="checkout-summary">
                <div class="summary-row">
                    <span><?php echo __('total_merchandise'); ?></span>
                    <span>₫<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                </div>
                <div class="summary-row">
                    <span><?php echo __('shipping_fee'); ?></span>
                    <span>₫0</span>
                </div>
                <div class="summary-row total-row">
                    <span><?php echo __('total_payment'); ?></span>
                    <span class="final-price">₫<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <div class="checkout-action">
                <p><?php echo __('order_agreement'); ?> <a href="#"><?php echo __('novel2x_terms'); ?></a></p>
                <button type="submit" class="btn-submit-order"><?php echo __('place_order'); ?></button>
            </div>
        </div>
    </form>
</div>

<!-- Shipping Edit Modal -->
<div id="shippingModal" class="store-modal">
    <div class="store-modal-content" style="width: 600px;">
        <div class="modal-header">
            <?php echo __('select_shipping_option'); ?>
        </div>
        <div class="modal-body">
            <div class="shipping-option-item active">
                <div class="option-info">
                    <div class="option-name"><?php echo __('self_delivery'); ?> <i class="fa-solid fa-check" style="color: #ee4d2d; margin-left: 10px;"></i></div>
                    <div class="option-desc"><?php echo __('delivery_desc'); ?></div>
                </div>
                <div class="option-price">₫0</div>
            </div>
            
            <div class="delivery-time-section">
                <div class="time-title">Thời gian giao hàng ưu tiên</div>
                <label class="time-option">
                    <input type="radio" name="delivery_time" value="anytime" checked>
                    <div class="time-info">
                        <strong>Giao hàng bất cứ lúc nào</strong>
                        <span>Thích hợp cho địa chỉ nhà riêng</span>
                    </div>
                </label>
                <label class="time-option">
                    <input type="radio" name="delivery_time" value="office">
                    <div class="time-info">
                        <strong>Giao hàng trong giờ hành chính</strong>
                        <span>Thích hợp cho địa chỉ văn phòng</span>
                    </div>
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeShippingModal()"><?php echo __('cancel'); ?></button>
            <button type="button" class="btn-confirm" onclick="saveShippingModal()"><?php echo __('confirm'); ?></button>
        </div>
    </div>
</div>

<!-- Address Edit Modal -->
<div id="addressModal" class="store-modal">
    <div class="store-modal-content">
        <div class="modal-header">
            <?php echo __('update_address'); ?>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group half">
                    <input type="text" id="modal_name" placeholder="<?php echo __('fullname'); ?>" value="<?php echo htmlspecialchars($buyer_name); ?>">
                </div>
                <div class="form-group half">
                    <input type="text" id="modal_phone" placeholder="<?php echo __('phone_number'); ?>" value="<?php echo htmlspecialchars($user_info['phone'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group" style="position:relative;">
                <textarea id="modal_address" rows="3" placeholder="<?php echo __('address_placeholder'); ?>"><?php echo htmlspecialchars($default_addr); ?></textarea>
                <button type="button" class="btn-open-map" onclick="openMapModal(event)" title="<?php echo __('select_on_map'); ?>" style="position: absolute; right: 10px; top: 10px; border: none; background: transparent; color: #ee4d2d; cursor: pointer; font-size: 16px;">
                    <i class="fa-solid fa-map-location-dot"></i>
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeAddressModal()"><?php echo __('back'); ?></button>
            <button type="button" class="btn-confirm" onclick="saveAddressModal()"><?php echo __('finish'); ?></button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/map_modal.php'; ?>
<script src="<?php echo $base_url; ?>public/assets/js/map_picker.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/checkout.js?v=<?php echo time(); ?>"></script>
</body>
</html>
