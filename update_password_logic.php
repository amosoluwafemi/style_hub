<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (empty($token) || empty($new_password)) {
        $_SESSION['error_msg'] = "Invalid request. Please try again.";
        header("Location: forgot_password.php");
        exit();
    }

    // 1. Verify the token one last time
    $stmt = $pdo->prepare("SELECT id FROM customers WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Hash the password for security
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // 3. Update password and CLEAR the reset token/expiry
        // This ensures the link can NEVER be used a second time
        $update = $pdo->prepare("UPDATE customers SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
        
        if ($update->execute([$hashed_password, $user['id']])) {
            $_SESSION['success_msg'] = "Password updated successfully! You can now login.";
            header("Location: login.php");
        } else {
            $_SESSION['error_msg'] = "Something went wrong. Please try again later.";
            header("Location: forgot_password.php");
        }
    } else {
        $_SESSION['error_msg'] = "This link has expired or is invalid.";
        header("Location: forgot_password.php");
    }
    exit();
}