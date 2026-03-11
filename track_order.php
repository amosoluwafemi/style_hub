<?php
require_once 'includes/db.php';
include 'includes/header.php';

$order = null;
$error = "";

if (isset($_GET['reference'])) {
    $ref = trim($_GET['reference']);
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE reference = ?");
    $stmt->execute([$ref]);
    $order = $stmt->fetch();
    
    if (!$order) { $error = "Order reference not found."; }
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-800 text-white">Track Your Style</h1>
        <p class="text-dim">Enter your reference number to see your order status.</p>
        
        <form action="" method="GET" class="d-flex justify-content-center mt-4">
            <input type="text" name="reference" class="form-control w-50 rounded-pill bg-dark text-white border-secondary" placeholder="e.g. SH-123456" value="<?php echo htmlspecialchars($_GET['reference'] ?? ''); ?>" required>
            <button type="submit" class="btn btn-primary rounded-pill ms-2 px-4">Track</button>
        </form>
    </div>

    <?php if ($order): ?>
        <?php
            // Logic to determine progress percentage
            $status = strtolower($order['status']);
            $steps = ['pending' => 10, 'processing' => 40, 'shipped' => 70, 'delivered' => 100];
            $progress = $steps[$status] ?? 0;
        ?>

        <div class="card bg-dark border-secondary rounded-4 p-4 shadow-lg">
            <div class="row mb-4">
                <div class="col-6">
                    <span class="text-dim small uppercase">Status</span>
                    <h4 class="text-white text-uppercase"><?php echo $status; ?></h4>
                </div>
                <div class="col-6 text-end">
                    <span class="text-dim small uppercase">Estimated Delivery</span>
                    <h4 class="text-white">3-5 Business Days</h4>
                </div>
            </div>

            <div class="progress bg-secondary mb-2" style="height: 10px; border-radius: 5px;">
                <div class="progress-bar bg-primary" style="width: <?php echo $progress; ?>%;"></div>
            </div>
            
            <div class="d-flex justify-content-between text-dim small">
                <span class="<?php if($progress >= 10) echo 'text-white fw-bold'; ?>">Ordered</span>
                <span class="<?php if($progress >= 40) echo 'text-white fw-bold'; ?>">Processing</span>
                <span class="<?php if($progress >= 70) echo 'text-white fw-bold'; ?>">Shipped</span>
                <span class="<?php if($progress >= 100) echo 'text-white fw-bold'; ?>">Delivered</span>
            </div>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>