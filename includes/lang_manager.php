<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$supported_langs = ['vi', 'en', 'ja'];
$default_lang = 'vi';

// 1. Language Detection
if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_langs)) {
    $lang = $_GET['lang'];
    setcookie('lang', $lang, time() + (86400 * 30), "/");
} elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $supported_langs)) {
    $lang = $_COOKIE['lang'];
} else {
    $lang = $default_lang;
}
$_SESSION['lang'] = $lang;

// 2. Currency Detection & Config
$available_currencies = [
    'VND' => [
        'symbol' => 'VNĐ',
        'name' => 'VietNam Dong',
        'rate' => 1,
        'pos' => 'after',
        'featured' => false
    ],
    'USD' => [
        'symbol' => '$',
        'name' => 'US Dollar',
        'rate' => 25400,
        'pos' => 'before',
        'featured' => false
    ],
    'EUR' => [
        'symbol' => '€',
        'name' => 'Euro',
        'rate' => 27500,
        'pos' => 'before',
        'featured' => true
    ],
    'JPY' => [
        'symbol' => '￥',
        'name' => 'Japanese Yen',
        'rate' => 165,
        'pos' => 'before',
        'featured' => true
    ]
];

if (isset($_GET['curr']) && isset($available_currencies[$_GET['curr']])) {
    $curr = $_GET['curr'];
    setcookie('curr', $curr, time() + (86400 * 30), "/");
} elseif (isset($_COOKIE['curr']) && isset($available_currencies[$_COOKIE['curr']])) {
    $curr = $_COOKIE['curr'];
} else {
    $curr = 'VND'; 
}
$_SESSION['curr'] = $curr;

// 3. Load translation file
$lang_file = __DIR__ . "/../languages/{$lang}.php";
$lang_data = file_exists($lang_file) ? include $lang_file : [];

/**
 * Translation helper function
 */
function __($key) {
    global $lang_data;
    return $lang_data[$key] ?? $key;
}

/**
 * Dynamic content helper (for DB fields)
 */
function __d($data, $field) {
    $lang = $_SESSION['lang'] ?? 'vi';
    $field_lang = $field . '_' . $lang;
    
    if ($lang !== 'vi' && !empty($data[$field_lang])) {
        return $data[$field_lang];
    }
    return $data[$field] ?? '';
}

/**
 * Price formatter helper with Dynamic Exchange Rates
 * Returns HTML to allow styling the symbol separately
 */
function __p($price_vnd) {
    global $available_currencies;
    $curr = $_SESSION['curr'] ?? 'VND';
    $config = $available_currencies[$curr] ?? $available_currencies['VND'];
    
    $rate = $config['rate'];
    $symbol = $config['symbol'];
    $pos = $config['pos'];
    
    // Quy đổi
    $converted = $price_vnd / $rate;
    
    // Định dạng số
    if ($curr === 'VND') {
        $formatted = number_format($converted, 0, ',', '.');
    } else {
        $formatted = number_format($converted, 2, '.', ',');
    }

    $symbol_html = '<span class="p-symbol" style="font-size: 0.6em; margin: 0 2px; vertical-align: super;">' . $symbol . '</span>';
    
    if ($pos === 'before') {
        return $symbol_html . $formatted;
    } else {
        return $formatted . $symbol_html;
    }
}
