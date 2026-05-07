<?php
// footer.php — NOVEL2X
?>

<!-- BACK TO TOP BAR -->
<div class="footer-back-to-top" id="backToTopBar">
    <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'}); return false;">
        <?php echo __('back_to_top'); ?>
    </a>
</div>

<footer class="site-footer">

    <!-- ===== LINKS GRID ===== -->
    <div class="footer-links-section">

        <div class="footer-col">
            <h4 class="footer-col-title"><?php echo __('about_us'); ?></h4>
            <ul>
                <li><a href="<?php echo $base_url; ?>about"><?php echo __('shop_intro'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>contact"><?php echo __('contact'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>blog"><?php echo __('blog'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>careers"><?php echo __('careers'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>press"><?php echo __('press'); ?></a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-col-title"><?php echo __('customer_support'); ?></h4>
            <ul>
                <li><a href="<?php echo $base_url; ?>faq"><?php echo __('faq'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>shipping"><?php echo __('shipping_policy'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>returns"><?php echo __('returns_exchanges'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>order-history"><?php echo __('order_history'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>contact"><?php echo __('send_feedback'); ?></a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-col-title"><?php echo __('explore'); ?></h4>
            <ul>
                <?php
                // In danh mục từ CSDL nếu có
                if (!empty($categories_header)):
                    foreach ($categories_header as $cat):
                ?>
                    <li>
                        <a href="<?php echo $base_url; ?>shop?category=<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars(__($cat['name'])); ?>
                        </a>
                    </li>
                <?php
                    endforeach;
                else:
                ?>
                    <li><a href="<?php echo $base_url; ?>shop"><?php echo __('all_products'); ?></a></li>
                    <li><a href="<?php echo $base_url; ?>shop?category=1"><?php echo __('Manga'); ?></a></li>
                    <li><a href="<?php echo $base_url; ?>shop?category=2"><?php echo __('Light Novel'); ?></a></li>
                    <li><a href="<?php echo $base_url; ?>shop?category=3"><?php echo __('Artbook'); ?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo $base_url; ?>shop?sort=new"><?php echo __('newest'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>shop?sort=bestseller"><?php echo __('bestseller'); ?></a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-col-title"><?php echo __('payment_shipping'); ?></h4>
            <ul>
                <li><a href="<?php echo $base_url; ?>payment"><?php echo __('payment_methods'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>shipping"><?php echo __('shipping_partners'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>security"><?php echo __('security_safety'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>gift-cards"><?php echo __('gift_cards'); ?></a></li>
                <li><a href="<?php echo $base_url; ?>promo"><?php echo __('today_deals'); ?></a></li>
            </ul>
            <!-- Payment icons -->
            <div class="footer-payment-icons">
                <span class="pay-badge">VNPay</span>
                <span class="pay-badge">COD</span>
                <span class="pay-badge">MoMo</span>
            </div>
        </div>

    </div><!-- /.footer-links-section -->

    <!-- ===== DIVIDER ===== -->
    <hr class="footer-divider">

    <!-- ===== MIDDLE BAR: Logo + Social + Lang ===== -->
    <div class="footer-mid-bar">

        <a href="<?php echo $base_url; ?>" class="footer-logo">
            <img src="<?php echo $base_url; ?>public/assets/img/images.jpg" alt="Novel2x Logo">
            <span class="footer-logo-text">Novel<span>2X</span></span>
        </a>

        <div class="footer-social">
            <a href="https://facebook.com" target="_blank" rel="noopener" title="Facebook" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="https://tiktok.com" target="_blank" rel="noopener" title="TikTok" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.19 8.19 0 0 0 4.79 1.53V6.75a4.85 4.85 0 0 1-1.02-.06z"/></svg>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener" title="Instagram" class="social-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
            <a href="https://x.com" target="_blank" rel="noopener" title="X" class="social-icon">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"></path></svg>
           </a>

            <a href="https://youtube.com" target="_blank" rel="noopener" title="YouTube" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white"/></svg>
            </a>
        </div>

        <div class="footer-lang-region">
            <div class="footer-lang-wrapper">
                <button class="lang-btn" id="footerLangBtn">
                    <span class="globe-icon">🌐</span>
                    <?php 
                        $current_lang = $_SESSION['lang'] ?? 'vi';
                        if ($current_lang == 'vi') echo 'Tiếng Việt';
                        elseif ($current_lang == 'en') echo 'English';
                        elseif ($current_lang == 'ja') echo '日本語 ';
                    ?>
                    <span class="lang-arrow">⇅</span>
                </button>
                <div class="ghost-bridge"></div>
                <div class="footer-lang-popover">
                    <p class="popover-title"><?php echo __('change_language'); ?></p>
                    <div class="lang-options">
                        <label class="lang-option">
                            <input type="radio" name="footer_lang" value="vi" <?php echo $current_lang == 'vi' ? 'checked' : ''; ?> onchange="changeLanguage('vi')">
                            <span class="lang-text">Tiếng Việt - VI</span>
                        </label>
                        <label class="lang-option">
                            <input type="radio" name="footer_lang" value="en" <?php echo $current_lang == 'en' ? 'checked' : ''; ?> onchange="changeLanguage('en')">
                            <span class="lang-text">English - EN</span>
                        </label>
                        <label class="lang-option">
                            <input type="radio" name="footer_lang" value="ja" <?php echo $current_lang == 'ja' ? 'checked' : ''; ?> onchange="changeLanguage('ja')">
                            <span class="lang-text">日本語 - JA</span>
                        </label>
                    </div>
                </div>
            </div>
            <button class="lang-btn">
                <?php echo __('region'); ?>: <?php echo __('nhatban'); ?>
            </button>
        </div>

        <script>
        function changeLanguage(lang) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('lang', lang);
            window.location.href = currentUrl.toString();
        }
        </script>

    </div><!-- /.footer-mid-bar -->

    <!-- ===== DIVIDER ===== -->
    <hr class="footer-divider footer-divider--thin">

    <!-- ===== BOTTOM BAR ===== -->
    <div class="footer-bottom-bar">
        <div class="footer-bottom-links">
            <a href="<?php echo $base_url; ?>terms"><?php echo __('terms_of_use'); ?></a>
            <span class="sep">|</span>
            <a href="<?php echo $base_url; ?>privacy"><?php echo __('privacy_policy'); ?></a>
            <span class="sep">|</span>
            <a href="<?php echo $base_url; ?>cookie"><?php echo __('cookie_policy'); ?></a>
            <span class="sep">|</span>
            <a href="<?php echo $base_url; ?>sitemap"><?php echo __('sitemap'); ?></a>
        </div>
        <p class="footer-copy">
            &copy; <?php echo date('Y'); ?> Novel2x. <?php echo __('all_rights_reserved'); ?>
        </p>
    </div>

</footer>

<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/footer.css">
