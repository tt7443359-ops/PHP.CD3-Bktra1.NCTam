<?php
session_start();
$_SESSION = array();
session_destroy();

// Ép Cookie hết hạn
setcookie("stored_email", "", time() - 3600, "/");
setcookie("stored_password", "", time() - 3600, "/");

//JS replace
echo "<script>
    window.location.replace('index.php');
</script>";
exit();
?>