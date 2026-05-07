<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/db.php';

$request_uri = isset($_GET['route']) ? rtrim($_GET['route'], '/') : '';

//Route tĩnh
switch ($request_uri) {
    case '':
    case 'home':
        require __DIR__ . '/pages/index.php';
        break;
    case 'shop':
        require __DIR__ . '/pages/shop/index.php';
        break;
    case 'login':
        require __DIR__ . '/pages/auth/login.php';
        break;
    case 'register':
        require __DIR__ . '/pages/auth/register.php';
        break;
    case 'profile':
        require __DIR__ . '/pages/user/profile.php';
        break;
    case 'cart':
        require __DIR__ . '/pages/shop/view_cart.php';
        break;
    case 'contact':
        require __DIR__ . '/pages/contact.php';
        break;
    case 'order-success':
        require __DIR__ . '/pages/user/order_success.php';
        break;
    case 'checkout':
        require __DIR__ . '/pages/user/checkout.php';
        break;
    case 'order-history':
        require __DIR__ . '/pages/user/order_history.php';
        break;
    case 'reset-password':
        require __DIR__ . '/pages/auth/reset-password.php';
        break;
    case 'logout':
        require __DIR__ . '/actions/logout.php';
        break;
    case 'settings/preferences':
        require __DIR__ . '/pages/settings/preferences.php';
        break;

    // Admin routes
    case 'admin':
    case 'admin/dashboard':
        require __DIR__ . '/admin/dashboard.php';
        break;
    case 'admin/products':
        require __DIR__ . '/admin/products/index.php';
        break;
    case 'admin/orders':
        require __DIR__ . '/admin/orders.php';
        break;
    case 'admin/messages':
        require __DIR__ . '/admin/messages.php';
        break;
    case 'admin/queries':
        require __DIR__ . '/admin/queries.php';
        break;
    case 'admin/cancel-requests':
        require __DIR__ . '/admin/cancel_requests.php';
        break;
    case 'admin/categories':
        require __DIR__ . '/admin/categories/index.php';
        break;
    case 'admin/create-category':
        require __DIR__ . '/admin/categories/create.php';
        break;
    case 'admin/create-product':
        require __DIR__ . '/admin/products/create.php';
        break;
    case 'admin/users': // New route for user management
        require __DIR__ . '/admin/users/index.php';
        break;
    case 'admin/create-user':
        require __DIR__ . '/admin/users/create.php';
        break;
    case 'admin/locked-users': // New route for locked users
        require __DIR__ . '/admin/users/locked.php';
        break;

    default:
        // Dynamic routes with regex
        if (preg_match('/^shop\/category\/([0-9]+)$/', $request_uri, $matches)) {
            $_GET['category'] = $matches[1];
            require __DIR__ . '/pages/shop/index.php';
        } elseif (preg_match('/^shop\/details\/([0-9]+)$/', $request_uri, $matches)) {
            $_GET['id'] = $matches[1];
            require __DIR__ . '/pages/shop/details.php';
        } elseif (preg_match('/^admin\/update-product\/([0-9]+)$/', $request_uri, $matches)) {
            $_GET['id'] = $matches[1];
            require __DIR__ . '/admin/products/update.php';
        } elseif (preg_match('/^admin\/update-category\/([0-9]+)$/', $request_uri, $matches)) {
            $_GET['id'] = $matches[1];
            require __DIR__ . '/admin/categories/update.php';
        } elseif (preg_match('/^admin\/delete-category\/([0-9]+)$/', $request_uri, $matches)) {
            $_GET['id'] = $matches[1];
            require __DIR__ . '/admin/categories/delete.php';
        } elseif (preg_match('/^admin\/update-user\/([0-9]+)$/', $request_uri, $matches)) { // New route for updating user
            $_GET['id'] = $matches[1];
            require __DIR__ . '/admin/users/update.php';
        } elseif (preg_match('/^admin\/lock-user\/([0-9]+)$/', $request_uri, $matches)) { // New route for locking user
            $_GET['id'] = $matches[1];
            require __DIR__ . '/actions/lock_user.php';
        } elseif (preg_match('/^admin\/restore-user\/([0-9]+)$/', $request_uri, $matches)) { // New route for restoring user
            $_GET['id'] = $matches[1];
            require __DIR__ . '/actions/restore_user.php';
        } elseif (preg_match('/^admin\/delete-user\/([0-9]+)$/', $request_uri, $matches)) { // New route for deleting user
            $_GET['id'] = $matches[1];
            require __DIR__ . '/actions/delete_user.php';
        } elseif (preg_match('/^request-cancel\/([0-9]+)$/', $request_uri, $matches)) {
            $_GET['id'] = $matches[1];
            require __DIR__ . '/pages/user/request_cancel.php';
        } else {
            // Not Found
            http_response_code(404);
            $errorMessage = "URL not found: /" . htmlspecialchars($request_uri);
            require __DIR__ . '/pages/notfound.php';
        }
        break;
}
?>