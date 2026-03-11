<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die("No reference found. Transaction failed.");
}

// 1. VERIFY THE TRANSACTION WITH PAYSTACK (Backend Verification)
$secret_key = 'sk_test_4a5394b2830850f197fa0c2645232f3adcf789ad'; // REPLACE WITH YOUR SECRET KEY
$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secret_key",
    "Cache-Control: no-cache",
]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result && $result['status'] && $result['data']['status'] === 'success') {
    // PAYMENT IS VALID!
    
    $customer_id = $_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? 0;
    $total_amount = $result['data']['amount'] / 100; // Convert back from kobo to Naira
    
    try {
        $pdo->beginTransaction();

        // 2. Insert into 'orders' table
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, reference, total_amount, status, created_at) VALUES (?, ?, ?, 'Paid', NOW())");
        $stmt->execute([$customer_id, $reference, $total_amount]);
        $order_id = $pdo->lastInsertId();

        // 3. Insert each cart item into 'order_items' table
        $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $qty = $item['qty'] ?? $item['quantity'] ?? 1;
            $item_stmt->execute([$order_id, $product_id, $qty, $item['price']]);
        }

        $pdo->commit();

        // 4. Clear the cart and redirect to success
        unset($_SESSION['cart']);
        // Inside your process_order.php success logic:
        header("Location: success.php?reference=" . $reference);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Database Error: " . $e->getMessage());
    }
} else {
    // Payment failed or was faked
    header("Location: checkout.php?error=payment_failed");
    exit();
}