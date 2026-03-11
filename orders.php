<?php 
require_once 'includes/db.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$customer_id = $_SESSION['user_id'] ?? $_SESSION['customer_id'] ?? $_SESSION['id'] ?? null;

if (!$customer_id) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="ambient-glow">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
</div>

<div class="container py-5 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-5 reveal">
        <div>
            <h2 class="fw-800 display-5 mb-0 text-white">Order History</h2>
            <p class="text-dim opacity-75">Track and manage your luxury acquisitions.</p>
        </div>
        <a href="shop.php" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Continue Shopping
        </a>
    </div>

    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $index => $order): ?>
            <div class="product-card mb-5 reveal delay-<?php echo ($index % 4) + 1; ?>">
                <div class="p-4 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-dim small text-uppercase fw-600">Reference:</span> 
                        <span class="fw-bold text-hub-gold ms-2"><?php echo $order['reference'] ?? 'N/A'; ?></span>
                    </div>
                    <div>
                        <span class="badge bg-white bg-opacity-10 text-white border border-secondary border-opacity-25 px-3 py-2 rounded-pill small">
                            <i class="bi bi-calendar3 me-2"></i><?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                        </span>
                    </div>
                </div>

                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0 text-white">
                            <thead>
                                <tr class="text-dim small text-uppercase border-bottom border-secondary border-opacity-10">
                                    <th class="pb-3">Product</th>
                                    <th class="pb-3 text-center">Choice</th>
                                    <th class="pb-3 text-center">Qty</th>
                                    <th class="pb-3 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $item_query = "SELECT * FROM order_items WHERE order_id = ?";
                                $item_stmt = $pdo->prepare($item_query);
                                $item_stmt->execute([$order['id']]);
                                $items = $item_stmt->fetchAll();

                                foreach ($items as $item):
                                ?>
                                <tr>
                                    <td class="py-3 fw-600"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-dark border border-secondary border-opacity-25 fw-normal">
                                            <?php echo htmlspecialchars($item['variant']); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 text-center text-dim">x<?php echo $item['quantity']; ?></td>
                                    <td class="py-3 text-end fw-bold text-hub-gold">₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-4 bg-white bg-opacity-5 border-top border-secondary border-opacity-10">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <span class="text-dim small text-uppercase fw-600 d-block mb-1">Shipping Status</span> 
                            <?php 
                                $status = strtolower($order['status'] ?? 'pending');
                                $statusMap = [
                                    'pending'   => ['class' => 'bg-warning text-dark', 'icon' => 'bi-clock-history'],
                                    'shipped'   => ['class' => 'bg-info text-white', 'icon' => 'bi-truck'],
                                    'delivered' => ['class' => 'bg-success text-white', 'icon' => 'bi-check-all']
                                ];
                                $current = $statusMap[$status] ?? $statusMap['pending'];
                            ?>
                            <span class="badge <?php echo $current['class']; ?> text-uppercase px-3 py-2" style="letter-spacing: 1px; font-size: 0.7rem;">
                                <i class="bi <?php echo $current['icon']; ?> me-1"></i> <?php echo $status; ?>
                            </span>
                        </div>
                        <div class="col-md-4 text-md-center mb-3 mb-md-0">
                            <span class="text-dim small text-uppercase fw-600 d-block mb-1">Total Paid</span>
                            <span class="h4 mb-0 text-dark fw-800">₦<?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button onclick="printOrder(<?php echo $order['id']; ?>)" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                <i class="bi bi-printer-fill me-2"></i>PRINT RECEIPT
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="product-card text-center py-5 reveal">
            <i class="bi bi-box-seam display-1 text-dim opacity-25"></i>
            <h4 class="mt-4 text-white">No orders found</h4>
            <p class="text-dim mb-4">Your collection is waiting for its first piece.</p>
            <a href="shop.php" class="btn btn-primary rounded-pill px-5 py-3 shadow-lg">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<script>
function printOrder(orderId) {
    const printWindow = window.open(`print_receipt.php?id=${orderId}`, '_blank', 'width=900,height=800');
    if(!printWindow) {
        alert("Please allow popups to print your receipt.");
    }
}
</script>

<script src="assets/js/main.js"></script>
</body>
</html>