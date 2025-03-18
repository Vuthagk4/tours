<?php
session_start();
$_SESSION = array(); // Clear session variables

// Destroy the session completely
session_destroy();

// Clear session cookies if they exist
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page
header("Location: login.php");
exit();
?>
