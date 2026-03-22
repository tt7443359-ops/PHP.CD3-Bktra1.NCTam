<?php
require_once("db.php"); 
require_once("auth_check.php");

$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // BẢO MẬT: mysqli_real_escape_string, tránh hack CSDL
    $name    = mysqli_real_escape_string($conn, $_POST["name"]);
    $email   = mysqli_real_escape_string($conn, $_POST["email"]);
    $subject = mysqli_real_escape_string($conn, $_POST["subject"]);
    $message = mysqli_real_escape_string($conn, $_POST["message"]);

    // Lấy email từ Cookie
    $cookie_email = $_COOKIE['stored_email'] ?? '';

    // 1. Kiểm tra định dạng mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $success = "Email không đúng định dạng!";
    } 
    elseif (empty($cookie_email)) {
        $success = "Lỗi: Bạn cần đăng ký/đăng nhập trước khi gửi liên hệ!";
    }
    elseif ($email !== $cookie_email) {
        $success = "Lỗi: Email không khớp với thông tin đã đăng ký!";
    } 
    else {
        //Câu lệnh SQL để "đẩy" dữ liệu vào bảng 'contacts csdl'
        $sql = "INSERT INTO contacts (fullname, email, subject, message) 
                VALUES ('$name', '$email', '$subject', '$message')";

        if (mysqli_query($conn, $sql)) {
            $success = "Gửi tin nhắn thành công!";
        } else {
            $success = "Lỗi CSDL: " . mysqli_error($conn);
        }
    }
}

$page_title = "Liên Hệ";
require_once "include/header.php";
?>
<link rel="stylesheet" href="contact.css">
<style>
    body {
        margin: 0;
        padding: 0;
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                          url('img/wp6271426.jpg') !important;
        
        /*Lặp lại ảnh */
        background-repeat: repeat !important; 
        background-size: 700px auto !important; 
        background-attachment: fixed !important;
    }
</style>

<div class="container" style="margin-top: 50px;">
    <h2>Liên Hệ Với Admin</h2>

    <?php if($success != "") echo "<div class='success'>$success</div>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Nhập họ tên" required>
        <input type="email" name="email" placeholder="Nhập email" required>
        <input type="text" name="subject" placeholder="Nhập chủ đề" required>
        <textarea name="message" rows="5" placeholder="Nhập nội dung tin nhắn" required></textarea>
        <button type="submit">Gửi Tin Nhắn</button>
    </form>
</div>

</body>
</html>