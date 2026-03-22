<?php
require_once 'db.php';
require_once("auth_check.php"); 
//Xóa tin
if (isset($_GET['delete'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql_delete = "DELETE FROM contacts WHERE id = $id_to_delete";
    mysqli_query($conn, $sql_delete);
    // Xóa xong thì load lại trang cho sạch
    header("Location: admin_messages.php");
    exit();
}

// Lấy toàn bộ danh sách liên hệ từ bảng contacts 'csdl'
$sql = "SELECT * FROM contacts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Liên hệ</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #46a5e5; color: white; }
        td a:hover {
            background-color: #ffcccc; 
            transition: 0.3s; 
        }
    </style>
</head>
<body>

    <h2>Danh sách tin nhắn từ khách hàng</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Chủ đề</th>
            <th>Nội dung</th>
            <th>Thời gian</th>
            <th><center>Thao tác</center></th></tr>
        
        <?php 
        // VÒNG LẶP: Duyệt từng dòng dữ liệu lấy được từ CSDL
        while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
            <td><?php echo $row['created_at']; ?></td>

        <td style="text-align: center; padding: 0;">
            <a href="admin_messages.php?delete=<?php echo $row['id']; ?>" 
               onclick="return confirm('Muốn xóa tin này?');" 
               style="color: red; text-decoration: none; font-weight: bold; display: block; padding: 10px; width: 89%; height: 100%;">
               Xóa
            </a>
        </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>