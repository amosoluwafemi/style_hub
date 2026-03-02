<?php
session_start();

if (isset($_GET['id'])) {
    $cart_key = $_GET['id']; // This is the unique key (e.g., "12_L")

    if (isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);
    }
}

// Redirect back to cart
header("Location: cart.php");
exit();