<?php
require_once 'db.php';

echo "<h2>Starting Password Migration...</h2>";

// 1. Đảm bảo cột password đủ dài (ít nhất 255 ký tự) để cất giữ chuỗi băm (60 ký tự)
mysqli_query($conn, "ALTER TABLE `admin` MODIFY `password` VARCHAR(255) NOT NULL");
mysqli_query($conn, "ALTER TABLE `users` MODIFY `password` VARCHAR(255) NOT NULL");

// 2. Migrate Admin
$sql_admin = "SELECT id, password FROM `admin`";
$res_admin = mysqli_query($conn, $sql_admin);
$admin_count = 0;

if ($res_admin) {
    while ($row = mysqli_fetch_assoc($res_admin)) {
        // Mật khẩu chưa băm thường ngắn và không bắt đầu bằng '$2y$'
        $is_hashed = (strlen($row['password']) === 60 && strpos($row['password'], '$2y$') === 0);
        
        if (!$is_hashed) { 
            $hashed = password_hash($row['password'], PASSWORD_DEFAULT);
            $safe_hashed = mysqli_real_escape_string($conn, $hashed);
            $id = $row['id'];
            $update = "UPDATE `admin` SET password = '$safe_hashed' WHERE id = $id";
            if (mysqli_query($conn, $update)) {
                $admin_count++;
                echo "Admin ID $id: Mật khẩu đã được băm.<br>";
            } else {
                echo "Admin ID $id: Lỗi cập nhật - " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "Admin ID {$row['id']}: Đã băm từ trước.<br>";
        }
    }
}
echo "<strong>Đã băm thành công cho $admin_count admin.</strong><br><br>";

// 3. Migrate Users
$sql_users = "SELECT id, password FROM `users`";
$res_users = mysqli_query($conn, $sql_users);
$user_count = 0;

if ($res_users) {
    while ($row = mysqli_fetch_assoc($res_users)) {
        // Kiểm tra xem mật khẩu đã băm hay chưa
        $is_hashed = (strlen($row['password']) === 60 && strpos($row['password'], '$2y$') === 0);
        
        if (!$is_hashed) {
            $hashed = password_hash($row['password'], PASSWORD_DEFAULT);
            $safe_hashed = mysqli_real_escape_string($conn, $hashed);
            $id = $row['id'];
            $update = "UPDATE `users` SET password = '$safe_hashed' WHERE id = $id";
            if (mysqli_query($conn, $update)) {
                $user_count++;
                echo "User ID $id: Mật khẩu đã được băm.<br>";
            } else {
                echo "User ID $id: Lỗi cập nhật - " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "User ID {$row['id']}: Đã băm từ trước.<br>";
        }
    }
}
echo "<strong>Đã băm thành công cho $user_count users.</strong><br><br>";

echo "<h3 style='color:green;'>Quá trình băm mật khẩu hoàn tất! Bạn vui lòng TRUY CẬP VÀO THƯ MỤC SOURCE CODE VÀ XÓA FILE <code>migrate_passwords.php</code> NÀY ĐỂ BẢO MẬT.</h3>";
echo "<a href='login.php'>Đến trang đăng nhập thử nghiệm</a>";
?>
