
<?php
// Set session name for user portal
session_name('UserSession');
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
