<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // 1. Find the user by email
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Verify the password hash
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct!
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name']; // Matches your DB 'full_name' column
        
        // Success redirect
        header("Location: shop.php");
        exit();
    } else {
        // Login failed
        $_SESSION['error_msg'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
}