<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// $base_url is now set globally in db.php
$total_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $total_items += $qty;
    }
}

$categories_header = [];
$cat_res_h = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY id ASC");
if ($cat_res_h && mysqli_num_rows($cat_res_h) > 0) {
    while ($c = mysqli_fetch_assoc($cat_res_h)) {
        $categories_header[] = $c;
    }
}

$header_category_id = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : '';
$header_search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

// Ẩn search bar trang liên hệ
$current_script = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
$show_search_bar = ($current_script !== 'contact.php');

$user_avatar = null;
$is_guest_user = isset($_SESSION['user']) || isset($_COOKIE['stored_email']);
$is_admin = isset($_SESSION['admin_logged_in']);
$is_logged_in = $is_guest_user || $is_admin;

// Lấy thông tin Tên/Avatar 
$display_name = "Khách Hàng";
if ($is_guest_user && isset($_SESSION['user'])) {
    $stmt = mysqli_prepare($conn, "SELECT fullname, email, avatar FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['user']);
    mysqli_stmt_execute($stmt);
    $h_res = mysqli_stmt_get_result($stmt);
    if ($h_row = mysqli_fetch_assoc($h_res)) {
        $display_name = !empty($h_row['fullname']) ? $h_row['fullname'] : $h_row['email'];
        $user_avatar = $base_url . 'public/assets/img/default_avatar.png';
        if (!empty($h_row['avatar']) && file_exists(__DIR__ . '/../public/uploads/avatars/' . $h_row['avatar'])) {
            $user_avatar = $base_url . 'public/uploads/avatars/' . $h_row['avatar'];
        }
    }
    mysqli_stmt_close($stmt);
} elseif ($is_admin) {
    $display_name = $_SESSION['admin_user'] ?? "Admin";
    $user_avatar = $base_url . 'public/assets/img/default_avatar.png';
    $admin_id = $_SESSION['admin_id'] ?? 0;

    if ($admin_id > 0) {
        $adm_res = mysqli_query($conn, "SELECT avatar, fullname FROM users WHERE id = $admin_id AND role='admin' LIMIT 1");
        if ($adm_row = @mysqli_fetch_assoc($adm_res)) {
            if (!empty($adm_row['fullname'])) {
                $display_name = $adm_row['fullname'];
                $_SESSION['admin_user'] = $display_name; // update session
            }
            if (!empty($adm_row['avatar']) && file_exists(__DIR__ . '/../public/uploads/avatars/' . $adm_row['avatar'])) {
                $user_avatar = $base_url . 'public/uploads/avatars/' . $adm_row['avatar'];
            }
        }
    }
}
$home_url = $base_url;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?php echo isset($page_title) ? $page_title : "Novel2x"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/logo.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/header.css">
</head>

<body>
    <header class="amz-header">

        <div class="nav-belt">
            <a href="<?php echo $base_url; ?>" class="nav-logo-container nav-border-hover">
                <img src="<?php echo $base_url; ?>public/assets/img/images.jpg" alt="Logo">
            </a>

            <a href="#" class="nav-global-location nav-border-hover">
                <div class="loc-icon-wrapper">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <div class="loc-text-wrapper">
                    <span class="nav-line-1"><?php echo __('deliver_to'); ?> <?php echo __('japan'); ?></span>
                    <span class="nav-line-2"><?php echo __('update_location'); ?></span>
                </div>
            </a>

            <?php if ($show_search_bar): ?>
                <div class="nav-fill">
                    <form method="GET" action="<?php echo $base_url; ?>shop" class="hi-search-form" id="headerSearchForm">
                        <div class="hi-cat-wrapper" id="catWrapper">
                            <button type="button" class="hi-cat-btn" id="catBtn" onclick="toggleCatDropdown()">
                                <span id="catBtnLabel"><?php echo __('all_categories'); ?></span>
                                <span class="arrow" id="catArrow">▼</span>
                            </button>
                            <div class="hi-cat-dropdown" id="catDropdown">
                                <div class="hi-cat-list" id="catList">
                                    <div class="cat-item<?php echo $header_category_id === '' ? ' selected' : ''; ?>"
                                        data-value="" onclick="selectCat(this, '', '<?php echo __('all_categories'); ?>')"><?php echo __('all_categories'); ?></div>
                                    <?php foreach ($categories_header as $cat): ?>
                                        <div class="cat-item<?php echo $header_category_id == $cat['id'] ? ' selected' : ''; ?>"
                                            data-value="<?php echo $cat['id']; ?>"
                                            onclick="selectCat(this, '<?php echo $cat['id']; ?>', '<?php echo htmlspecialchars(__($cat['name']), ENT_QUOTES); ?>')">
                                            <?php echo htmlspecialchars(__($cat['name'])); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <input type="hidden" name="category" id="catValueInput"
                                value="<?php echo htmlspecialchars($header_category_id); ?>">
                        </div>
                        <input type="text" name="search" class="hi-search-input" placeholder="<?php echo __('search_placeholder'); ?>"
                            value="<?php echo htmlspecialchars($header_search_keyword ?? ''); ?>" autocomplete="off">
                        <button type="submit" class="hi-search-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                            </svg>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="nav-fill"></div>
            <?php endif; ?>

            <div class="nav-tools">
                <div class="nav-tools-item nav-border-hover">
                    <div class="nav-line-1" style="height:14px;"></div>
                    <div class="nav-line-2" style="display:flex; align-items:center;">
                        <img src="<?php echo $base_url; ?>public/assets/img/JP.jpg"
                            style="width:18px; margin-right:4px;" alt="">
                        <span class="arrow-down"></span>
                    </div>
                    <div class="nav-popover lang-popover">
                        <div class="popover-row" style="color:#555;"><?php echo __('change_language'); ?></div>
                        <div class="popover-row">
                            <label style="cursor:pointer; display:block; width:100%;" onclick="window.location.href='?lang=vi'">
                                <input type="radio" name="lang" <?php echo $_SESSION['lang'] === 'vi' ? 'checked' : ''; ?>> Tiếng Việt - VI
                            </label>
                        </div>
                        <div class="popover-row">
                            <label style="cursor:pointer; display:block; width:100%;" onclick="window.location.href='?lang=en'">
                                <input type="radio" name="lang" <?php echo $_SESSION['lang'] === 'en' ? 'checked' : ''; ?>> English - EN
                            </label>
                        </div>
                        <div class="popover-row">
                            <label style="cursor:pointer; display:block; width:100%;" onclick="window.location.href='?lang=ja'">
                                <input type="radio" name="lang" <?php echo $_SESSION['lang'] === 'ja' ? 'checked' : ''; ?>> 日本語 - JA
                            </label>
                        </div>
                        <hr>
                        <div class="popover-row" style="color:#555;"><?php echo __('change_currency'); ?></div>
                        <?php 
                            $current_curr = $_SESSION['curr'] ?? 'VND';
                            $count = 0;
                            foreach($available_currencies as $code => $cfg): 
                                if(($cfg['featured'] || $code === $current_curr) && $count < 2):
                                    $count++;
                        ?>
                            <div class="popover-row">
                                <label style="cursor:pointer; display:block; width:100%;" onclick="window.location.href='?curr=<?php echo $code; ?>'">
                                    <input type="radio" <?php echo $code === $current_curr ? 'checked' : ''; ?>> 
                                    <?php echo $cfg['symbol'] . " - " . $code . " - " . $cfg['name']; ?>
                                </label>
                            </div>
                        <?php 
                                endif;
                            endforeach; 
                        ?>
                        
                        <div class="popover-row" style="margin-top:5px;">
                            <a href="<?php echo $base_url; ?>settings/preferences" style="text-decoration:none; color:#007185; font-size:12px;">
                                <?php echo __('view_more'); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="nav-tools-item nav-border-hover">
                    <div class="nav-line-1"><?php echo __('hello'); ?>,
                        <?php echo $is_logged_in ? htmlspecialchars($display_name) : __('sign_in'); ?>
                    </div>
                    <div class="nav-line-2" style="font-weight:700;"><?php echo __('account_lists'); ?> <span
                            class="arrow-down"></span></div>
                    <div class="nav-popover account-popover">
                        <?php if (!$is_logged_in): ?>
                            <div class="acc-sign-in">
                                <a href="<?php echo $base_url; ?>login" class="amz-btn-yellow"><?php echo __('sign_in'); ?></a>
                                <p><?php echo __('new_customer'); ?> <a href="<?php echo $base_url; ?>login"><?php echo __('start_here'); ?></a></p>
                            </div>
                            <hr>
                        <?php endif; ?>
                        <div class="acc-cols">
                            <div class="acc-col">
                                <h3><?php echo __('your_lists'); ?></h3>
                                <a href="#"><?php echo __('create_wishlist'); ?></a>
                                <a href="#"><?php echo __('find_gift'); ?></a>
                                <a href="#"><?php echo __('explore_showroom'); ?></a>
                            </div>
                            <div class="acc-col acc-col-right">
                                <h3><?php echo __('your_account'); ?></h3>
                                <?php if ($is_logged_in): ?>
                                    <a href="<?php echo $base_url; ?>profile"><?php echo __('profile'); ?></a>
                                    <?php if (!$is_admin): ?>
                                        <a href="<?php echo $base_url; ?>order-history"><?php echo __('order_history'); ?></a>
                                    <?php endif; ?>
                                    <?php if ($is_admin): ?>
                                        <a href="<?php echo $base_url; ?>admin/dashboard"><?php echo __('dashboard'); ?></a>
                                        <a href="<?php echo $base_url; ?>admin/products"><?php echo __('manage_products'); ?></a>
                                        <a href="<?php echo $base_url; ?>admin/orders"><?php echo __('manage_orders'); ?></a>
                                    <?php endif; ?>
                                    <a href="<?php echo $base_url; ?>logout" style="margin-top:10px; color:#c45500;"><?php echo __('logout'); ?></a>
                                <?php else: ?>
                                    <a href="<?php echo $base_url; ?>login"><?php echo __('your_account'); ?></a>
                                    <a href="<?php echo $base_url; ?>login"><?php echo __('your_orders'); ?></a>
                                    <a href="<?php echo $base_url; ?>login"><?php echo __('your_wishlist'); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($is_admin): ?>
                    <a href="<?php echo $base_url; ?>admin/orders" class="nav-tools-item nav-border-hover">
                        <div class="nav-line-1"><?php echo __('manage_orders'); ?></div>
                        <div class="nav-line-2" style="font-weight:700;"><?php echo __('manage_orders'); ?></div>
                    </a>
                    <a href="<?php echo $base_url; ?>admin/products" class="nav-tools-item nav-border-hover">
                        <div class="nav-line-1"><?php echo __('manage_products'); ?></div>
                        <div class="nav-line-2" style="font-weight:700;"><?php echo __('manage_products'); ?></div>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $is_logged_in ? ($base_url . 'order-history') : ($base_url . 'login'); ?>"
                        class="nav-tools-item nav-border-hover">
                        <div class="nav-line-1"><?php echo __('returns_orders'); ?></div>
                        <div class="nav-line-2" style="font-weight:700;">& <?php echo __('your_orders'); ?></div>
                    </a>
                    <a href="<?php echo $base_url; ?>cart" class="nav-tools-item nav-border-hover nav-cart-wrapper">
                        <span class="nav-cart-count"><?php echo $total_items; ?></span>
                        <span class="nav-cart-icon">🛒</span>
                        <span class="nav-cart-text"><?php echo __('cart'); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="nav-main-bottom">
            <a href="javascript:void(0)" class="nav-all-btn nav-border-hover" onclick="toggleSidebar()">
                <span>☰</span> <?php echo __('all'); ?>
            </a>
            <a href="<?php echo $home_url; ?>" class="nav-border-hover"><?php echo __('home'); ?></a>
            <a href="<?php echo $base_url; ?>shop" class="nav-border-hover"><?php echo __('products'); ?></a>
            <a href="#" class="nav-border-hover"><?php echo __('today_deals'); ?></a>
            <a href="#" class="nav-border-hover"><?php echo __('customer_service'); ?></a>
            <a href="#" class="nav-border-hover"><?php echo __('gift_cards'); ?></a>
        </div>
    </header>

    <div id="sidebarMenu" class="sidebar-menu">
        <div class="sidebar-header">
            <button class="sidebar-close" onclick="toggleSidebar()">✖</button>
            <?php if ($is_logged_in): ?>
                <div class="sidebar-user-info">
                    <img src="<?php echo $user_avatar ? $user_avatar : ($base_url . 'public/assets/img/default_avatar.png'); ?>"
                        alt="Avatar" class="sidebar-avatar">
                    <span class="sidebar-username"><?php echo htmlspecialchars($display_name); ?></span>
                </div>
            <?php else: ?>
                <div class="sidebar-user-info">
                    <span class="sidebar-username"><?php echo __('sign_in'); ?></span>
                </div>
            <?php endif; ?>
        </div>
        <div class="sidebar-body">
            <?php if ($is_logged_in): ?>
                <?php if ($is_guest_user && !$is_admin): ?>
                    <a href="<?php echo $base_url; ?>profile"><?php echo __('profile'); ?></a>
                    <a href="<?php echo $base_url; ?>order-history"><?php echo __('order_history'); ?></a>
                    <a href="<?php echo $base_url; ?>contact"><?php echo __('contact'); ?></a>
                <?php endif; ?>

                <?php if ($is_admin): ?>
                    <a href="<?php echo $base_url; ?>admin/dashboard"><?php echo __('dashboard'); ?></a>
                    <a href="<?php echo $base_url; ?>admin/products"><?php echo __('manage_products'); ?></a>
                    <a href="<?php echo $base_url; ?>admin/categories"><?php echo __('manage_categories'); ?></a>
                    <a href="<?php echo $base_url; ?>admin/orders"><?php echo __('manage_orders'); ?></a>
                    <a href="<?php echo $base_url; ?>admin/users"><?php echo __('manage_users'); ?></a>
                    <a href="<?php echo $base_url; ?>admin/messages"><?php echo __('customer_feedback'); ?></a>
                    <a href="<?php echo $base_url; ?>admin/cancel-requests"><?php echo __('cancel_requests'); ?></a>
                    <a href="<?php echo $base_url; ?>profile"><?php echo __('profile'); ?></a>
                <?php endif; ?>
                <a href="<?php echo $base_url; ?>logout"><?php echo __('logout'); ?></a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>login"><?php echo __('sign_in'); ?></a>
                <a href="<?php echo $base_url; ?>login"><?php echo __('profile'); ?></a>
                <a href="<?php echo $base_url; ?>contact"><?php echo __('contact'); ?></a>
            <?php endif; ?>
        </div>
    </div>
    <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <script src="<?php echo $base_url; ?>public/assets/js/header.js"></script>
</body>

</html>