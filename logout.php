<?php
session_start();

// Clear all session variables
$_SESSION = array();

// If you want to kill the session cookie as well (extra security)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login or home page
header("Location: login.php?msg=Logged out successfully");
exit();