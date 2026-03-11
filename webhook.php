<?php
require_once 'includes/db.php';

// 1. Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit();
}

// 2. Retrieve the request body
$input = file_get_contents("php://input");

// 3. Verify the signature (Security check)
// This ensures the request actually came from Paystack and not a hacker
$paystack_sk = getenv('PAYSTACK_SECRET_KEY');
define('PAYSTACK_SECRET_KEY', $paystack_sk);

if (!$_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] || ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY))) {
    exit(); // Silent exit if signature is invalid
}

// 4. Parse the data
$event = json_decode($input);

// 5. Handle the 'charge.success' event
if ($event->event === 'charge.success') {
    $reference = $event->data->reference;
    $amount = $event->data->amount / 100; // Paystack sends in kobo
    $email = $event->data->customer->email;

    // 6. Update the order status in your database
    $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE reference = ? AND status = 'pending'");
    $stmt->execute([$reference]);
    
    // Optional: Log successful webhook for your records
    error_log("Webhook Success: Order $reference marked as PAID.");
}

// 7. Always respond with 200 OK so Paystack stops retrying
http_response_code(200);
?>