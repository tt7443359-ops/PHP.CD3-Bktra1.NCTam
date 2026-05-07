<?php
require_once __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/header.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lang'])) {
        $new_lang = $_POST['lang'];
        $_SESSION['lang'] = $new_lang;
        setcookie('lang', $new_lang, time() + (86400 * 30), "/");
    }
    if (isset($_POST['curr'])) {
        $new_curr = $_POST['curr'];
        $_SESSION['curr'] = $new_curr;
        setcookie('curr', $new_curr, time() + (86400 * 30), "/");
    }
    // Refresh to apply
    echo "<script>window.location.href='" . $base_url . "settings/preferences';</script>";
    exit;
}

$current_lang = $_SESSION['lang'] ?? 'vi';
$current_curr = $_SESSION['curr'] ?? 'VND';
?>

<style>
    .pref-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
    }

    .pref-header {
        border-bottom: 1px solid #ddd;
        padding-bottom: 15px;
        margin-bottom: 30px;
    }

    .pref-header h1 {
        font-size: 28px;
        margin: 0;
    }

    .pref-section {
        margin-bottom: 40px;
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 40px;
    }

    .pref-label h2 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .pref-label p {
        font-size: 14px;
        color: #555;
        line-height: 1.5;
    }

    .pref-options {
        background: #fff;
    }

    .option-row {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }

    .option-row input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .pref-footer {
        border-top: 1px solid #ddd;
        padding-top: 20px;
        margin-top: 40px;
        display: flex;
        gap: 15px;
    }

    .btn-cancel {
        padding: 8px 20px;
        border: 1px solid #d5d9d9;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-save {
        padding: 8px 20px;
        background: #ffd814;
        border: 1px solid #fcd200;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-save:hover {
        background: #f7ca00;
    }

    select.curr-select {
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ddd;
        width: 300px;
        font-size: 14px;
        margin-top: 15px;
    }

    .note-box {
        background: #f8f8f8;
        padding: 15px;
        border-radius: 4px;
        margin-top: 20px;
        font-size: 13px;
        color: #555;
        line-height: 1.6;
    }
</style>

<div class="pref-container">
    <div class="pref-header">
        <h1><?php echo __('Language Settings'); ?></h1>
    </div>

    <form method="POST">
        <?php echo csrf_tag(); ?>
        <!-- Language Section -->
        <div class="pref-section">
            <div class="pref-label">
                <h2><?php echo __('Language Settings'); ?></h2>
                <p><?php echo __('pref_lang_desc'); ?></p>
            </div>
            <div class="pref-options">
                <div class="option-row">
                    <input type="radio" name="lang" value="vi" id="lang-vi" <?php echo $current_lang === 'vi' ? 'checked' : ''; ?>>
                    <label for="lang-vi">Tiếng Việt - VI</label>
                </div>
                <div class="option-row">
                    <input type="radio" name="lang" value="en" id="lang-en" <?php echo $current_lang === 'en' ? 'checked' : ''; ?>>
                    <label for="lang-en">English - EN - <?php echo __('translation'); ?></label>
                </div>
                <div class="option-row">
                    <input type="radio" name="lang" value="ja" id="lang-ja" <?php echo $current_lang === 'ja' ? 'checked' : ''; ?>>
                    <label for="lang-ja">日本語 - JA</label>
                </div>
            </div>
        </div>

        <div style="border-bottom: 1px solid #eee; margin: 30px 0;"></div>

        <!-- Currency Section -->
        <div class="pref-section">
            <div class="pref-label">
                <h2><?php echo __('Currency Settings'); ?></h2>
                <p><?php echo __('pref_curr_desc'); ?></p>
            </div>
            <div class="pref-options">
                <?php foreach($available_currencies as $code => $cfg): ?>
                    <?php if($cfg['featured']): ?>
                    <div class="option-row">
                        <input type="radio" name="curr" value="<?php echo $code; ?>" 
                               id="curr-<?php echo strtolower($code); ?>" 
                               <?php echo $current_curr === $code ? 'checked' : ''; ?>
                               onclick="syncSelect(this.value)">
                        <label for="curr-<?php echo strtolower($code); ?>"><?php echo $cfg['symbol'] . " - " . $code . " - " . $cfg['name']; ?></label>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <select class="curr-select" id="currSelectMain" name="curr" onchange="syncRadio(this.value)">
                    <?php foreach($available_currencies as $code => $cfg): ?>
                        <option value="<?php echo $code; ?>" <?php echo $current_curr === $code ? 'selected' : ''; ?>>
                            <?php echo $cfg['symbol'] . " - " . $code . " - " . $cfg['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <script>
                function syncRadio(val) {
                    var radio = document.getElementById('curr-' + val.toLowerCase());
                    if (radio) radio.checked = true;
                }
                function syncSelect(val) {
                    var select = document.getElementById('currSelectMain');
                    if (select) select.value = val;
                }
                </script>

                <div class="note-box">
                    <?php echo str_replace('{curr}', $_SESSION['curr'], __('pref_note')); ?>
                    <a href="#" style="color:#007185;">Details</a>
                </div>
            </div>
        </div>

        <div class="pref-footer">
            <button type="button" class="btn-cancel"
                onclick="window.history.back();"><?php echo __('cancel'); ?></button>
            <button type="submit" class="btn-save"><?php echo __('Save Changes'); ?></button>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>