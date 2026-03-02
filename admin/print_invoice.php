<?php 
require_once '../includes/db.php'; 
require_once 'auth_check.php'; 

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Order Info
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) { die("Order not found."); }

// Fetch Order Items
$item_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$item_stmt->execute([$order_id]);
$items = $item_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .invoice-box {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: #fff;
            border: 1px solid #eee;
        }
        @media print {
            .no-print { display: none; }
            body { background: #fff; }
            .invoice-box { border: none; margin: 0; padding: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="container no-print mt-4 text-center">
    <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
    <a href="view_orders.php" class="btn btn-secondary">Back to Orders</a>
</div>

<div class="invoice-box shadow-sm">
    <div class="row mb-4">
        <div class="col-6">
            <h2 class="fw-bold">STYLE HUB</h2>
            <p class="text-muted">Your Premium Fashion Store<br>Ondo State, Nigeria</p>
        </div>
        <div class="col-6 text-end">
            <h4 class="text-uppercase text-muted">Invoice</h4>
            <p><strong>Order ID:</strong> #<?php echo $order_id; ?><br>
            <strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
        </div>
    </div>

    <hr>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="text-muted text-uppercase">Bill To:</h6>
            <p><strong><?php echo $order['customer_name']; ?></strong><br>
            <?php echo $order['email']; ?><br>
            <?php echo nl2br($order['address']); ?></p>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Item Description</th>
                <th>Size/Variant</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['product_name']; ?></td>
                <td><?php echo $item['variant']; ?></td>
                <td class="text-center">₦<?php echo number_format($item['price'], 2); ?></td>
                <td class="text-center"><?php echo $item['quantity']; ?></td>
                <td class="text-end">₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Grand Total:</th>
                <th class="text-end text-primary h5">₦<?php echo number_format($order['total_amount'], 2); ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="mt-5 text-center text-muted">
        <p>Thank you for shopping with Style Hub!</p>
        <small>For inquiries, contact us at support@stylehub.com</small>
    </div>
</div>

</body>
</html>