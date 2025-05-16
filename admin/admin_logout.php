<?php
session_name('AdminSession');
session_start();

// Verify CSRF token

session_destroy();
header("Location: admin_auth.php");
exit();
?>