<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
restrictToAdmin();

echo "Running DB checks...<br>";

// Migration gộp bảng customers vào users
$res = mysqli_query($conn, "SHOW TABLES LIKE 'customers'");
if (mysqli_num_rows($res) > 0) {
    echo "Found customers table, migrating...<br>";
    try {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN fullname VARCHAR(255) DEFAULT ''");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT ''");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN address TEXT");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT 'default_avatar.png'");
    } catch(Exception $e) { echo "Columns already added to users.<br>"; }

    try {
        mysqli_query($conn, "UPDATE users u JOIN customers c ON u.id = c.user_id SET u.fullname = c.fullname, u.phone = c.phone, u.address = c.address");
        $chk_avatar = mysqli_query($conn, "SHOW COLUMNS FROM customers LIKE 'avatar'");
        if(mysqli_num_rows($chk_avatar) > 0) {
            mysqli_query($conn, "UPDATE users u JOIN customers c ON u.id = c.user_id SET u.avatar = c.avatar");
        }
    } catch(Exception $e) {}

    try {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id");
    } catch(Exception $e) {}

    try {
        mysqli_query($conn, "UPDATE orders o JOIN customers c ON o.customer_id = c.id SET o.user_id = c.user_id");
    } catch(Exception $e) {}

    try {
        $fk_res = mysqli_query($conn, "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = 'orders' AND REFERENCED_TABLE_NAME = 'customers'");
        while ($row = mysqli_fetch_assoc($fk_res)) {
            $fk = $row['CONSTRAINT_NAME'];
            mysqli_query($conn, "ALTER TABLE orders DROP FOREIGN KEY $fk");
        }
        mysqli_query($conn, "ALTER TABLE orders DROP COLUMN customer_id");
        mysqli_query($conn, "ALTER TABLE orders ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        mysqli_query($conn, "DROP TABLE customers");
        echo "Customers table dropped and migrated to users successfully.<br>";
    } catch(Exception $e) { echo "Error migrating orders/customers: " . $e->getMessage() . "<br>"; }
} else {
    echo "Customers table not found. Migration already completed.<br>";
}

// Canceled items checking
try {
    mysqli_query($conn, "ALTER TABLE orders ADD COLUMN cancel_reason TEXT NULL");
} catch (Exception $e) {}
try {
    mysqli_query($conn, "ALTER TABLE orders ADD COLUMN cancel_request TINYINT(1) NOT NULL DEFAULT 0");
} catch (Exception $e) {}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS canceled_items ( id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, product_id INT NOT NULL, product_name VARCHAR(255) NOT NULL, quantity INT NOT NULL, price_at_buy DECIMAL(15,2) NOT NULL, cancel_reason TEXT, canceled_at DATETIME DEFAULT CURRENT_TIMESTAMP, KEY (order_id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

try {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN cart_data TEXT DEFAULT NULL");
} catch (Exception $e) {}
echo "DB Setup complete.<br>";
// Update path
echo "<br><a href='" . $base_url . "admin/dashboard'>Quay lại Dashboard</a>";
?>
