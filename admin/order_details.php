<?php 
require_once '../includes/db.php'; 

$order_id = $_GET['id'];

// Get Order Info
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

// Get Items in that Order
$item_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$item_stmt->execute([$order_id]);
$items = $item_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="card shadow border-0">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">Order #<?php echo $order_id; ?> Summary</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Customer Info:</h5>
                    <p><strong>Name:</strong> <?php echo $order['customer_name']; ?><br>
                    <strong>Email:</strong> <?php echo $order['email']; ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Shipping Address:</h5>
                    <p><?php echo nl2br($order['address']); ?></p>
                </div>
            </div>

            <table class="table border">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant (Size/Vol)</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><span class="badge bg-secondary"><?php echo $item['variant']; ?></span></td>
                        <td>₦<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h4 class="text-end">Total: ₦<?php echo number_format($order['total_amount'], 2); ?></h4>
        </div>
    </div>
    <div class="mt-3">
        <a href="view_orders.php" class="btn btn-secondary">Back to Orders</a>
    </div>
</div>
</body>
</html>