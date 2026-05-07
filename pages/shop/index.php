<?php
require_once __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/header.php";

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$sort = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'featured';

$products = [];
$total_results = 0;

$query = "SELECT p.*, c.name as category_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
         WHERE p.status = 1";

if (!empty($search)) {
    $query .= " AND p.name LIKE '%$search%'";
}
if (!empty($category)) {
    $query .= " AND p.category_id = '$category'";
}

// sắp xếp
$order_by = "p.id DESC"; // Mặc định: Nổi bật/Mới nhất
if ($sort == 'price_asc') $order_by = "p.price ASC";
if ($sort == 'price_desc') $order_by = "p.price DESC";

$query .= " ORDER BY $order_by";
$res = mysqli_query($conn, $query);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $products[] = $row;
    }
    $total_results = count($products);
}
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/index1.css">

<div id="shop-custom-home" class="search-results-mode">
    <!-- --- GIAO DIỆN KẾT QUẢ TÌM KIẾM --- -->
    <div class="results-container">
        <!-- Sidebar Lọc -->
        <aside class="results-sidebar">
            <div class="filter-section">
                <h3><?php echo __('free_shipping'); ?></h3>
                <label><input type="checkbox"> <?php echo __('free_shipping_cond'); ?></label>
                <p class="filter-text"><?php echo __('free_shipping_desc'); ?></p>
            </div>

            <div class="filter-section">
                <h3><?php echo __('delivery_day'); ?></h3>
                <label><input type="checkbox"> <?php echo __('deliver_tomorrow'); ?></label>
            </div>

            <div class="filter-section">
                <h3><?php echo __('all_categories'); ?></h3>
                <ul>
                    <li><a href="<?php echo $base_url; ?>shop?category=" class="<?php echo ($category === '') ? 'active' : ''; ?>"><?php echo __('all_products'); ?></a></li>
                    <?php 
                    $cat_res = mysqli_query($conn, "SELECT * FROM categories");
                    while($c = mysqli_fetch_assoc($cat_res)): 
                    ?>
                        <li><a href="<?php echo $base_url; ?>shop?category=<?php echo $c['id']; ?>" class="<?php echo $category == $c['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars(__($c['name'])); ?>
                        </a></li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <div class="filter-section">
                <h3><?php echo __('condition'); ?></h3>
                <ul>
                    <li><a href="#"><?php echo __('new'); ?></a></li>
                    <li><a href="#"><?php echo __('used'); ?></a></li>
                    <li><a href="#"><?php echo __('collectible'); ?></a></li>
                </ul>
            </div>
        </aside>

        <!-- Danh sách sản phẩm -->
        <main class="results-main">
            <div class="results-header-bar">
                <span class="results-count">1-<?php echo count($products); ?> <?php echo __('of'); ?> <?php echo $total_results; ?> <?php echo __('results_for'); ?> <?php if(!empty($search)) echo '<span class="search-term">"' . htmlspecialchars($search) . '"</span>'; ?></span>
                <div class="sort-dropdown">
                    <!-- Form for sorting that keeps existing query params -->
                    <form method="GET" action="<?php echo $base_url; ?>shop" style="display: inline;">
                        <?php if(!empty($search)): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
                        <?php if(!empty($category)): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>"><?php endif; ?>
                        <label><?php echo __('sort_by'); ?>: </label>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="featured" <?php echo $sort == 'featured' ? 'selected' : ''; ?>><?php echo __('featured'); ?></option>
                            <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>><?php echo __('price_low_to_high'); ?></option>
                            <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>><?php echo __('price_high_to_low'); ?></option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="results-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $p): ?>
                        <div class="result-card">
                            <div class="p-img-wrapper">
                                <?php if (rand(0, 5) > 3): ?>
                                    <div class="amz-badge-best"><?php echo __('best_seller'); ?></div>
                                <?php endif; ?>
                                <a href="<?php echo $base_url; ?>shop/details/<?php echo $p['id']; ?>">
                                    <img src="<?php echo $base_url; ?>public/assets/products_img/<?php echo $p['image_id']; ?>" alt="<?php echo htmlspecialchars(__d($p, 'name')); ?>">
                                </a>
                            </div>
                            <div class="p-info">
                                <h2 class="p-title">
                                    <a href="<?php echo $base_url; ?>shop/details/<?php echo $p['id']; ?>">
                                        <?php echo htmlspecialchars(__d($p, 'name')); ?>
                                    </a>
                                </h2>
                                <div class="p-rating">
                                    <span class="stars">★★★★☆</span> <span class="rating-count">(<?php echo rand(50, 500); ?>)</span>
                                </div>
                                <div class="p-price-row">
                                    <span class="p-price-main"><?php echo __p($p['price']); ?></span>
                                    <?php if (rand(0, 1)): ?>
                                        <span class="p-discount"><?php echo rand(10, 100); ?> (<?php echo rand(1, 10); ?>%) <?php echo __('save'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-prime-row">
                                    <span class="p-delivery"><?php echo __('deliver_tomorrow'); ?>, 22 thg 4</span>
                                </div>
                                <div class="p-stock">
                                    <?php echo __('only_left'); ?> <?php echo $p['stock_quantity']; ?> <?php echo __('order_now'); ?>.
                                </div>
                                <div class="p-actions">
                                    <?php if ($is_logged_in): ?>
                                    <a href="<?php echo $base_url; ?>actions/add_to_cart.php?id=<?php echo $p['id']; ?>" class="amz-btn-add"><?php echo __('add_to_cart'); ?></a>
                                    <?php elseif ($is_admin): ?>
                                    <a href="<?php echo $base_url; ?>admin/update-product/<?php echo $p['id']; ?>" class="amz-btn-add"><?php echo __('edit'); ?></a>
                                    <?php else: ?>
                                    <!-- Khách -->
                                    <a href="<?php echo $base_url; ?>login?action=add_to_cart&id=<?php echo $p['id']; ?>" class="amz-btn-add"><?php echo __('add_to_cart'); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <p><?php echo __('no_products_found'); ?></p>
                        <p><?php echo __('try_again'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
