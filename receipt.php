<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$order_id = $_GET['id'] ?? null;
$loggedInId = $_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if (!$order_id || !$loggedInId) {
    die("Access Denied.");
}

// Fetch Order & Customer Details
$stmt = $pdo->prepare("SELECT o.*, c.full_name, c.email, c.address 
                       FROM orders o 
                       JOIN customers c ON o.customer_id = c.id 
                       WHERE o.id = ? AND o.customer_id = ?");
$stmt->execute([$order_id, $loggedInId]);
$order = $stmt->fetch();

if (!$order) die("Order not found.");

// Fetch Order Items
$item_stmt = $pdo->prepare("SELECT oi.*, p.name 
                            FROM order_items oi 
                            JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?");
$item_stmt->execute([$order_id]);
$items = $item_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - <?php echo $order['reference']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
        .receipt-card { max-width: 800px; margin: 40px auto; background: white; padding: 50px; border-radius: 0; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .brand-logo { font-weight: 800; font-size: 24px; letter-spacing: 2px; }
        .status-badge { text-transform: uppercase; font-size: 12px; font-weight: 700; padding: 5px 15px; border: 1px solid #dee2e6; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .receipt-card { box-shadow: none; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="container no-print mt-4 text-center">
    <button onclick="window.print()" class="btn btn-dark rounded-pill px-4">
        <i class="bi bi-printer me-2"></i> Print or Save as PDF
    </button>
</div>

<div class="receipt-card">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <div class="brand-logo text-dark">STYLE HUB</div>
            <p class="text-muted small mb-0">Lagos, Nigeria</p>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-0">INVOICE</h4>
            <p class="text-muted small">#<?php echo $order['reference']; ?></p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-6">
            <p class="text-muted small mb-1">Billed To:</p>
            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($order['full_name']); ?></h6>
            <p class="small text-muted"><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
        </div>
        <div class="col-6 text-end">
            <p class="text-muted small mb-1">Date Issued:</p>
            <h6 class="fw-bold mb-0"><?php echo date('F d, Y', strtotime($order['created_at'])); ?></h6>
            <span class="status-badge mt-2 d-inline-block"><?php echo $order['status']; ?></span>
        </div>
    </div>

    <table class="table table-borderless">
        <thead class="border-bottom">
            <tr>
                <th class="py-3">Item Description</th>
                <th class="py-3 text-center">Qty</th>
                <th class="py-3 text-end">Price</th>
                <th class="py-3 text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
            <tr class="border-bottom">
                <td class="py-3"><?php echo htmlspecialchars($item['name']); ?></td>
                <td class="py-3 text-center"><?php echo $item['quantity']; ?></td>
                <td class="py-3 text-end">₦<?php echo number_format($item['price'], 2); ?></td>
                <td class="py-3 text-end fw-bold">₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end py-4 fw-bold">Grand Total:</td>
                <td class="text-end py-4 fw-bold h4 text-primary">₦<?php echo number_format($order['total_amount'], 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-5 pt-5 border-top text-center">
        <p class="small text-muted">Thank you for choosing Style Hub. For support, contact: 08110206999</p>
    </div>
</div>

</body>
</html>