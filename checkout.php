<?php
require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to cart if it's empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// 1. UNIVERSAL LOGIN CHECK (Synced with Header)
$loggedInId = $_SESSION['user_id'] ?? $_SESSION['customer_id'] ?? $_SESSION['id'] ?? null;

$cust_name = "";
$cust_email = "";
$cust_address = "";

// 2. Fetch profile details
if ($loggedInId) {
    $stmt = $pdo->prepare("SELECT full_name, email, address FROM customers WHERE id = ?");
    $stmt->execute([$loggedInId]);
    $user = $stmt->fetch();

    if ($user) {
        $cust_name = $user['full_name'];
        $cust_email = $user['email'];
        $cust_address = $user['address'];
    }
}

$total = 0;
foreach($_SESSION['cart'] as $item) {
    $qty = $item['qty'] ?? $item['quantity'] ?? 1;
    $total += $item['price'] * $qty;
}

include 'includes/header.php'; // Using your dynamic header
?>

<div class="ambient-glow">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
</div>

<div class="container py-5 mt-4">
    <div class="row g-5">
        <div class="col-md-5 order-md-2 mb-4 reveal">
            <div class="product-card p-4">
                <h4 class="d-flex justify-content-between align-items-center mb-4 text-white">
                    <span class="fw-800">Your Order</span>
                    <span class="badge bg-primary rounded-pill fs-6"><?php echo count($_SESSION['cart']); ?></span>
                </h4>
                
                <div class="order-items-scroll mb-3" style="max-height: 400px; overflow-y: auto;">
                    <?php foreach($_SESSION['cart'] as $item): 
                        $item_qty = $item['qty'] ?? $item['quantity'] ?? 1;
                    ?>
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-secondary border-opacity-10">
                        <div class="d-flex align-items-center">
                            <div class="position-relative">
                                <img src="assets/images/products/<?php echo $item['image']; ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px;" 
                                     class="border border-secondary border-opacity-25">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark border border-secondary" style="font-size: 0.6rem;">
                                    <?php echo $item_qty; ?>
                                </span>
                            </div>
                            <div class="ms-3">
                                <h6 class="my-0 text-white fw-600"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-dim"><?php echo htmlspecialchars($item['variant'] ?? 'Standard'); ?></small>
                            </div>
                        </div>
                        <span class="text-white fw-bold">₦<?php echo number_format($item['price'] * $item_qty, 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="pt-3">
                    <div class="d-flex justify-content-between text-dim mb-2">
                        <span>Subtotal</span>
                        <span>₦<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-dim mb-3">
                        <span>Shipping</span>
                        <span class="text-success small fw-bold">Calculated at next step</span>
                    </div>
                    <div class="d-flex justify-content-between border-top border-secondary border-opacity-25 pt-3">
                        <span class="fw-800 text-white h5">Total</span>
                        <strong class="text-hub-gold h4 fw-800">₦<?php echo number_format($total, 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7 order-md-1 reveal delay-1">
            <h4 class="mb-4 fw-800 text-white display-6">Shipping Details</h4>
            
            <?php if(!$loggedInId): ?>
                <div class="product-card p-3 mb-4 bg-primary bg-opacity-10 border-primary border-opacity-25">
                    <p class="mb-0 text-white small">
                        <i class="bi bi-lightning-charge-fill text-hub-gold me-2"></i>
                        Returning customer? <a href="login.php" class="text-hub-gold fw-bold text-decoration-none">Login</a> for faster checkout.
                    </p>
                </div>
            <?php endif; ?>

            <form id="paymentForm" class="product-card p-4 border-0">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label text-dim small fw-600 text-uppercase">Full Name</label>
                        <input type="text" id="full-name" class="form-control bg-dark bg-opacity-25 border-secondary border-opacity-25 text-white py-3 rounded-3 shadow-none focus-ring" value="<?php echo htmlspecialchars($cust_name); ?>" placeholder="Akinbobola..." required>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-dim small fw-600 text-uppercase">Email Address</label>
                        <input type="email" id="email-address" class="form-control bg-dark bg-opacity-25 border-secondary border-opacity-25 text-white py-3 rounded-3 shadow-none" value="<?php echo htmlspecialchars($cust_email); ?>" placeholder="email@example.com" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-dim small fw-600 text-uppercase">Delivery Address</label>
                        <textarea id="delivery-address" class="form-control bg-dark bg-opacity-25 border-secondary border-opacity-25 text-white py-3 rounded-3 shadow-none" rows="3" placeholder="Full street address, city, and state" required><?php echo htmlspecialchars($cust_address); ?></textarea>
                    </div>
                </div>

                <div class="mt-5 mb-4">
                    <h6 class="fw-800 text-white mb-3">Secure Payment</h6>
                    <div class="p-3 rounded-4 border border-primary border-opacity-25 bg-primary bg-opacity-5 d-flex align-items-center">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="payment" id="paystack" checked>
                        </div>
                        <div class="ms-3">
                            <label class="form-check-label text-white fw-600 d-block" for="paystack">Online Payment (Paystack)</label>
                            <small class="text-dim">Card, Transfer, USSD, or Bank</small>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-shield-lock-fill text-success fs-4"></i>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-800 shadow-lg mt-2" type="button" onclick="payWithPaystack()">
                    <i class="bi bi-credit-card-2-front me-2"></i> PAY NOW ₦<?php echo number_format($total, 2); ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function payWithPaystack() {
    const btn = event.target.closest('button');
    const email = document.getElementById('email-address').value;
    const amount = <?php echo $total; ?>;
    const name = document.getElementById('full-name').value;
    const address = document.getElementById('delivery-address').value;

    if(!email || !name || !address) {
        alert("Please complete your shipping details.");
        return;
    }

    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-grow spinner-grow-sm me-2"></span> INITIALIZING...';

    let handler = PaystackPop.setup({
        key: 'pk_test_1ceebd1ca46e77c62fa497b0020bd2d910596ad5',
        email: email,
        amount: amount * 100,
        currency: 'NGN',
        ref: 'SH-' + Math.floor((Math.random() * 1000000000) + 1),
        metadata: {
            custom_fields: [
                { display_name: "Customer Name", variable_name: "customer_name", value: name },
                { display_name: "Delivery Address", variable_name: "delivery_address", value: address }
            ]
        },
        callback: function(response) {
            window.location.href = "process_order.php?reference=" + response.reference;
        },
        onClose: function() {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    });
    handler.openIframe();
}
</script>
</body>
</html>