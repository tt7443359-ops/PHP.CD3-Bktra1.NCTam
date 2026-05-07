<?php
require_once __DIR__ . "/../includes/db.php";
include __DIR__ . "/../includes/header.php";
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/index1.css">

<div id="shop-custom-home">
    <!-- --- BANNERS CHÍNH (hiện ở trang chủ) --- -->
    <section class="hero-container">
        <div class="hero-slider">
            <div class="hero-slide"><img src="<?php echo $base_url; ?>public/assets/img/51rqfd+BdxL._SX1500_.jpg"></div>
            <div class="hero-slide"><img src="<?php echo $base_url; ?>public/assets/img/51t9fGAZWIL._SX3000_.jpg"></div>
            <div class="hero-slide"><img src="<?php echo $base_url; ?>public/assets/img/61lwJy4B8PL._SX3000_.jpg"></div>
            <div class="hero-slide"><img src="<?php echo $base_url; ?>public/assets/img/images (1).jpg" width="3000px" height="400px"></div>
        </div>
        <div class="hero-controls">
            <button class="hero-btn" onclick="moveHero(-1)">&#10094;</button>
            <button class="hero-btn" onclick="moveHero(1)">&#10095;</button>
        </div>
    </section>

    <!-- --- NỘI DUNG TRANG CHỦ --- -->
    <main class="main-content">
        <div class="grid-layout">
            <!-- ROW 1: 4 SINGLE IMAGE CARDS -->
            <div class="amz-card">
                <h2><?php echo __('kindle_manga_title'); ?></h2>
                <a href="#" class="single-image-link">
                    <img src="<?php echo $base_url; ?>public/assets/img/GW_HudCard_1xresizeA._SY304_CB428334697_.jpg">
                </a>
                <div class="card-link"><a href="#"><?php echo __('view_popular_titles'); ?></a></div>
            </div>

            <div class="amz-card">
                <h2><?php echo __('blooming_points_title'); ?></h2>
                <a href="#" class="single-image-link">
                    <img src="<?php echo $base_url; ?>public/assets/img/PF4-11-009_GW_379x304._SY304_CB783468938_.jpg">
                </a>
                <div class="card-link"><a href="#"><?php echo __('view_more'); ?></a></div>
            </div>

            <div class="amz-card">
                <h2><?php echo __('bestsellers_by_cat'); ?></h2>
                <a href="#" class="single-image-link">
                    <img src="<?php echo $base_url; ?>public/assets/img/bestseller_379x304._SY304_CB542011244_.jpg">
                </a>
                <div class="card-link"><a href="#"><?php echo __('explore_now'); ?></a></div>
            </div>

            <div class="amz-card">
                <h2><?php echo __('prime_reading_title'); ?></h2>
                <a href="#" class="single-image-link">
                    <img src="<?php echo $base_url; ?>public/assets/img/Prime-Reading_CP_230829-GWCard-379x304._SY304_CB597477864_.jpg">
                </a>
                <div class="card-link"><a href="#"><?php echo __('view_more'); ?></a></div>
            </div>

            <!-- ROW 2: SHOVELERS -->
            <div class="shoveler-container">
                <div class="shoveler-header">
                    <h2><?php echo __('recommendations_for_you'); ?></h2>
                    <a href="<?php echo $base_url; ?>shop"><?php echo __('view_more'); ?></a>
                </div>
                <div class="shoveler-viewport">
                    <button class="shoveler-btn btn-left" onclick="scrollShoveler(-1, this)">&#10094;</button>
                    <div class="shoveler-list">
                        <?php
                        $ids_string = "12, 17, 10, 19, 8, 7, 11, 18, 16"; 
                        $query = "SELECT * FROM products WHERE id IN ($ids_string) AND status = 1 ORDER BY FIELD(id, $ids_string)";
                        $res = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($res ?: [])):
                        ?>
                            <div class="product-item">
                                <a href="<?php echo $base_url; ?>shop/details/<?php echo $row['id']; ?>">
                                    <img src="<?php echo $base_url; ?>public/assets/products_img/<?php echo $row['image_id']; ?>">
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <button class="shoveler-btn btn-right" onclick="scrollShoveler(1, this)">&#10095;</button>
                </div>
            </div>

            <div class="shoveler-container">
                <div class="shoveler-header">
                    <h2><?php echo __('interested_products'); ?></h2>
                    <a href="<?php echo $base_url; ?>shop"><?php echo __('view_more'); ?></a>
                </div>
                <div class="shoveler-viewport">
                    <button class="shoveler-btn btn-left" onclick="scrollShoveler(-1, this)">&#10094;</button>
                    <div class="shoveler-list">
                        <?php
                        $ids_string = "6, 20, 9, 13, 14 ,15, 5, 4, 3, 2, 1"; 
                        $query = "SELECT * FROM products WHERE id IN ($ids_string) AND status = 1 ORDER BY FIELD(id, $ids_string)";
                        $res = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($res ?: [])):
                        ?>
                            <div class="product-item">
                                <a href="<?php echo $base_url; ?>shop/details/<?php echo $row['id']; ?>">
                                    <img src="<?php echo $base_url; ?>public/assets/products_img/<?php echo $row['image_id']; ?>">
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <button class="shoveler-btn btn-right" onclick="scrollShoveler(1, this)">&#10095;</button>
                </div>
            </div>

            <!-- ROW 3: QUAD CARDS -->
            <div class="amz-card">
                <h2><?php echo __('continue_shopping'); ?></h2>
                <div class="quad-grid">
                    <div class="quad-item">
                        <a href="#" class="quad-item">
                        <img src="https://images-fe.ssl-images-amazon.com/images/G/09/DynamicScheduling/Uploaded-HIT-Images/Babel-ffbc6d28-9448-4a8d-9e8f-0cee37c12681/2025-07-07-01/fluid-quad-image-label_Desktop_B0CBFGMDGJ_1x._SY116_CB790199480_.jpg">
                        <span><?php echo __('elderly'); ?></span>
                        </a>
                    </div>
                    <a href="#" class="quad-item">
                        <img src="https://images-fe.ssl-images-amazon.com/images/G/09/DynamicScheduling/Uploaded-HIT-Images/Babel-ffbc6d28-9448-4a8d-9e8f-0cee37c12681/2025-07-07-01/fluid-quad-image-label_Desktop_B0CHXVBQHR_1x._SY116_CB790199481_.jpg">
                        <span><?php echo __('men'); ?></span>
                    </a>
                    <a href="#" class="quad-item">
                        <img src="https://images-fe.ssl-images-amazon.com/images/G/09/DynamicScheduling/Uploaded-HIT-Images/Babel-ffbc6d28-9448-4a8d-9e8f-0cee37c12681/2025-07-07-01/fluid-quad-image-label_Desktop_B01N5K834V_1x._SY116_CB790199434_.jpg">
                        <span><?php echo __('women'); ?></span>
                    </a>
                    <a href="#" class="quad-item">
                        <img src="https://images-fe.ssl-images-amazon.com/images/G/09/DynamicScheduling/Uploaded-HIT-Images/Babel-ffbc6d28-9448-4a8d-9e8f-0cee37c12681/2025-07-07-01/fluid-quad-image-label_Desktop_B0CM6L34W4_1x._SY116_CB790199473_.jpg">
                        <span><?php echo __('kids'); ?></span>
                    </a>
                </div>
                <div class="card-link"><a href="#"><?php echo __('view_more'); ?></a></div>
            </div>

            <div class="amz-card">
                <h2><?php echo __('similar_products'); ?></h2>
                <div class="quad-grid">
                    <a href="<?php echo $base_url; ?>shop/details/12" class="quad-item">
                        <img src="<?php echo $base_url; ?>public/assets/products_img/81uqY2PZQ0L._SL1500_.jpg">
                        <span>Hell's Paradise: Jigokuraku, Vol. 2 (2)</span>
                    </a>
                    <a href="<?php echo $base_url; ?>shop/details/13" class="quad-item">
                        <img src="<?php echo $base_url; ?>public/assets/products_img/415ICRO8pYL._SY445_SX342_ML2_.jpg">
                        <span>Another(下) (角川文庫)</span>
                    </a>
                    <a href="<?php echo $base_url; ?>shop/details/14" class="quad-item">
                        <img src="<?php echo $base_url; ?>public/assets/products_img/41JW63K3TFL._SY445_SX342_ML2_.jpg">
                        <span>殺人鬼 ‐‐覚醒篇 (角川文庫 あ 45-5)</span>
                    </a>
                    <a href="<?php echo $base_url; ?>shop/details/15" class="quad-item">
                        <img src="<?php echo $base_url; ?>public/assets/products_img/41-MlfibiPL._SY445_SX342_ML2_.jpg">
                        <span>Another(上) (角川文庫)</span>
                    </a>
                </div>
                <div class="card-link"><a href="#"><?php echo __('view_more'); ?></a></div>
            </div>

            <div class="amz-card">
                <h2><?php echo __('recommendations_for_you'); ?></h2>
                <div class="quad-grid">
                    <a href="#" class="quad-item">
                        <img src="https://images-fe.ssl-images-amazon.com/images/G/09/DynamicScheduling/Uploaded-HIT-Images/Babel-e5f51c4b-8756-44b2-83b2-d681790f58e4/2024-11-07-23/fluid-quad-image-label_Desktop_B0D4M6W6GX_1x._SY116_CB543066331_.jpg">
                        <span><?php echo __('hobby_leisure'); ?></span>
                    </a>
                    <a href="#" class="quad-item">
                        <img src="https://images-fe.ssl-images-amazon.com/images/G/09/DynamicScheduling/Uploaded-HIT-Images/Babel-e5f51c4b-8756-44b2-83b2-d681790f58e4/2024-11-07-23/fluid-quad-image-label_Desktop_B0DC5YV1TD_1x._SY116_CB543077493_.jpg">
                        <span><?php echo __('home_appliances'); ?></span>
                    </a>
                    <a href="#" class="quad-item">
                        <img src="https://images-fe.ssl-images-amazon.com/images/G/09/DynamicScheduling/Uploaded-HIT-Images/Babel-e5f51c4b-8756-44b2-83b2-d681790f58e4/2024-11-07-23/fluid-quad-image-label_Desktop_B0DFBZZPHP_1x._SY116_CB543066548_.jpg">
                        <span><?php echo __('beauty_pets'); ?></span>
                    </a>
                    <a href="#" class="quad-item">
                        <img src="https://images-fe.ssl-images-amazon.com/images/G/09/DynamicScheduling/Uploaded-HIT-Images/Babel-e5f51c4b-8756-44b2-83b2-d681790f58e4/2024-11-07-23/fluid-quad-image-label_Desktop_B0DGQ7MX3D_1x._SY116_CB543077545_.jpg">
                        <span><?php echo __('fashion'); ?></span>
                    </a>
                </div>
                <div class="card-link"><a href="#"><?php echo __('view_more'); ?></a></div>
            </div>

            <div class="amz-card">
                <h2><?php echo __('earn_points_title'); ?></h2>
                <a href="#" class="single-image-link" style="margin-bottom: 10px; flex-grow:0; height: auto;">
                    <img src="https://m.media-amazon.com/images/I/31W55czULxL._SY160_.jpg" style="height: 160px; background: #f7f7f7; width: 100%;">
                    <span style="display:block; padding: 5px 0; font-size: 14px;"><?php echo __('points_promo'); ?></span>
                </a>
                <div class="quad-grid" style="grid-template-columns: repeat(3, 1fr);">
                    <a href="#" class="quad-item">
                        <img src="https://m.media-amazon.com/images/I/21AP+kxI6vL._SY75_.jpg" style="background:#f7f7f7; padding: 5px; aspect-ratio: 1/1;">
                        <span style="font-size: 11px; height: auto; text-align: center;"><?php echo __('campaign'); ?></span>
                    </a>
                    <a href="#" class="quad-item">
                        <img src="https://m.media-amazon.com/images/I/21FWra2zUuL._SY75_.jpg" style="background:#f7f7f7; padding: 5px; aspect-ratio: 1/1;">
                        <span style="font-size: 11px; height: auto; text-align: center;"><?php echo __('guide'); ?></span>
                    </a>
                    <a href="#" class="quad-item">
                        <img src="https://m.media-amazon.com/images/I/21oXuhDGjOL._SY75_.jpg" style="background:#f7f7f7; padding: 5px; aspect-ratio: 1/1;">
                        <span style="font-size: 11px; height: auto; text-align: center;"><?php echo __('my_points'); ?></span>
                    </a>
                </div>
                <div class="card-link"><a href="#"><?php echo __('check_points'); ?></a></div>
            </div>
        </div>
    </main>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/index1.js"></script>

<?php include __DIR__ . "/../includes/footer.php"; ?>
