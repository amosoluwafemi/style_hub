<?php
require_once 'includes/db.php';


$orderId = $_GET['id'] ?? null;
$customer_id = $_SESSION['user_id'] ?? $_SESSION['customer_id'] ?? null;

if (!$orderId || !$customer_id) die("Access Denied");

// Fetch Order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
$stmt->execute([$orderId, $customer_id]);
$order = $stmt->fetch();

// Fetch Items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt_#<?= $orderId ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: white; color: black; font-family: 'Segoe UI', sans-serif; }
        .receipt-header { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container py-5" style="max-width: 800px;">
        <div class="no-print mb-4">
            <button onclick="window.close()" class="btn btn-secondary">← Close Preview</button>
        </div>

        <div class="receipt-header d-flex justify-content-between align-items-end">
            <div>
                <h1 class="fw-bold mb-0">STYLE HUB</h1>
                <p class="text-muted">Premium Fashion & Skincare</p>
            </div>
            <div class="text-end">
                <h4 class="mb-0">OFFICIAL RECEIPT</h4>
                <p class="mb-0">Order ID: #<?= $order['id'] ?></p>
                <p>Date: <?= date('M d, Y', strtotime($order['created_at'])) ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h6 class="text-uppercase text-muted small">Billed To:</h6>
                <p class="fw-bold mb-0"><?= $_SESSION['full_name'] ?? 'Valued Customer' ?></p>
                <p><?= nl2br(htmlspecialchars($order['address'] ?? 'No address provided')) ?></p>
            </div>
            <div class="col-6 text-end">
                <h6 class="text-uppercase text-muted small">Payment Status:</h6>
                <p class="text-success fw-bold">PAID (via Paystack)</p>
                <p class="small text-muted">Ref: <?= $order['reference'] ?></p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Item Description</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?= $item['product_name'] ?> <br><small class='text-muted'><?= $item['variant'] ?></small></td>
                    <td class="text-center">₦<?= number_format($item['price'], 2) ?></td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-end">₦<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Grand Total:</th>
                    <th class="text-end">₦<?= number_format($order['total_amount'], 2) ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="mt-5 text-center">
            <p class="small text-muted">Thank you for shopping with Style Hub!</p>
            <p class="tiny">This is a computer-generated receipt.</p>
        </div>
    </div>
</body>
</html>