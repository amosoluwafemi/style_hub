<?php
require_once 'includes/db.php';

// Fix for session notice: Only start if one doesn't exist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Capture the ID from your product_details.php form
    $p_id = $_POST['product_id']; 
    $variant = $_POST['selected_variant'];
    $qty = (int)$_POST['qty'];

    // Create a unique key for the cart (Product ID + Size)
    $cart_id = $p_id . "_" . $variant;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$cart_id])) {
        $_SESSION['cart'][$cart_id]['qty'] += $qty;
    } else {
        // Fetch product details for the cart display
        $stmt = $pdo->prepare("SELECT name, base_price, image_url FROM products WHERE id = ?");
        $stmt->execute([$p_id]);
        $product = $stmt->fetch();

        // THE CRITICAL FIX: We MUST include 'product_id' here so place_order.php can see it
        $_SESSION['cart'][$cart_id] = [
            'product_id' => $p_id, // THIS WAS MISSING
            'name' => $product['name'],
            'price' => $product['base_price'],
            'image' => $product['image_url'],
            'variant' => $variant,
            'qty' => $qty
        ];
    }

    header("Location: cart.php");
    exit();
}