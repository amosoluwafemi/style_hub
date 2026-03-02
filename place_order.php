<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['cart'])) {
    $total_amount = $_POST['total_amount'] ?? 0;
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $address = htmlspecialchars($_POST['address'] ?? '');

    try {
        $pdo->beginTransaction();

        foreach ($_SESSION['cart'] as $item) {
            // FIX: Check if product_id exists in the session item
            if (!isset($item['product_id'])) {
                throw new Exception("Cart Error: Missing 'product_id' for " . $item['name'] . ". Please clear your cart and try again.");
            }

            // Sync Check
            $stmt = $pdo->prepare("SELECT stock_qty FROM product_variants WHERE product_id = ? AND attribute_value = ?");
            $stmt->execute([$item['product_id'], $item['variant']]);
            $variant_stock = $stmt->fetchColumn();

            if ($variant_stock === false || $variant_stock < $item['qty']) {
                $count = ($variant_stock === false) ? 0 : $variant_stock;
                throw new Exception("Stock sync error: " . $item['name'] . " (" . $item['variant'] . ") only has $count left.");
            }
        }

        // Create Order
        $order_stmt = $pdo->prepare("INSERT INTO orders (customer_id, customer_name, email, address, total_amount) VALUES (?, ?, ?, ?, ?)");
        $order_stmt->execute([$_SESSION['customer_id'] ?? null, $name, $email, $address, $total_amount]);
        $order_id = $pdo->lastInsertId();

        foreach ($_SESSION['cart'] as $item) {
            // Record Item
            $pdo->prepare("INSERT INTO order_items (order_id, product_name, variant, price, quantity) VALUES (?, ?, ?, ?, ?)")
                ->execute([$order_id, $item['name'], $item['variant'], $item['price'], $item['qty']]);

            // Sync User Side (Variants)
            $pdo->prepare("UPDATE product_variants SET stock_qty = stock_qty - ? WHERE product_id = ? AND attribute_value = ?")
                ->execute([$item['qty'], $item['product_id'], $item['variant']]);

            // Sync Admin Side (Main Products - image_9ebafb.png)
            $pdo->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?")
                ->execute([$item['qty'], $item['product_id']]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        echo "<script>alert('Order Successful! Your order has been recieved, wait it is been processed.'); window.location.href='shop.php';</script>";

    } catch (Exception $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        echo "<script>alert('" . $e->getMessage() . "'); window.location.href='cart.php';</script>";
        exit();
    }
}