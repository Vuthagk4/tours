
<?php
session_name('AdminSession');
session_start();

// Verify CSRF token

session_destroy();
header("Location: admin_login.php");
exit();
?>
