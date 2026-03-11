<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Get the reference from the URL
$reference = $_GET['reference'] ?? null;
$order = null;

if ($reference) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE reference = ? LIMIT 1");
    $stmt->execute([$reference]);
    $order = $stmt->fetch();
}
?>

<style>
    .success-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 30px;
        padding: 3rem;
        text-align: center;
        margin-top: 50px;
    }
    .check-icon {
        font-size: 5rem;
        color: #d4af37; /* Style Hub Gold */
        animation: scaleUp 0.5s ease-out;
    }
    @keyframes scaleUp {
        0% { transform: scale(0); }
        100% { transform: scale(1); }
    }
    .order-number {
        letter-spacing: 2px;
        color: rgba(255,255,255,0.6);
        font-size: 0.9rem;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="success-card shadow-lg">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill check-icon"></i>
                </div>
                
                <h2 class="fw-800 text-white mb-2">Order Confirmed</h2>
                <p class="text-dim mb-4">Thank you for choosing Style Hub. Your luxury pieces are being prepared for shipment.</p>

                <?php if($order): ?>
                    <div class="bg-dark bg-opacity-25 rounded-3 p-3 mb-4 border border-secondary border-opacity-10">
                        <span class="order-number text-uppercase">Order Reference</span>
                        <h5 class="text-white mb-0 mt-1">#<?php echo htmlspecialchars($order['reference']); ?></h5>
                    </div>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <a href="shop.php" class="btn btn-primary btn-lg rounded-pill fw-700 py-3">
                        CONTINUE SHOPPING
                    </a>
                    <a href="track_order.php" class="btn btn-outline-secondary btn-sm border-0 text-dim mt-2">
                        Track My Delivery
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>