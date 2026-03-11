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

/** * PROFESSIONAL TIP: We pull the Public Key from the environment 
 * This ensures your new Paystack account works immediately!
 */
$paystack_pk = getenv('PAYSTACK_PUBLIC_KEY') ?: 'pk_test_1ceebd1ca46e77c62fa497b0020bd2d910596ad5';

// 1. UNIVERSAL LOGIN CHECK
$loggedInId = $_SESSION['user_id'] ?? $_SESSION['customer_id'] ?? $_SESSION['id'] ?? null;

$cust_name = "";
$cust_email = "";
$cust_address = "";

// 2. Fetch profile details (Prevents the "Empty Name" issue in Admin)
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

include 'includes/header.php'; 
?>

<style>
    .product-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; }
    .text-dim { color: rgba(255,255,255,0.6); }
    .focus-ring:focus { border-color: #d4af37; box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25); }
    .text-hub-gold { color: #d4af37; }
</style>

<div class="container py-5 mt-4">
    <div class="row g-5">
        <div class="col-md-5 order-md-2 mb-4">
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
                            <img src="assets/images/products/<?php echo $item['image']; ?>" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px;" 
                                 class="border border-secondary border-opacity-25">
                            <div class="ms-3">
                                <h6 class="my-0 text-white fw-600"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-dim">Qty: <?php echo $item_qty; ?></small>
                            </div>
                        </div>
                        <span class="text-white fw-bold">₦<?php echo number_format($item['price'] * $item_qty, 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="pt-3 border-top border-secondary border-opacity-25">
                    <div class="d-flex justify-content-between border-top border-secondary border-opacity-25 pt-3">
                        <span class="fw-800 text-white h5">Total</span>
                        <strong class="text-hub-gold h4 fw-800">₦<?php echo number_format($total, 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7 order-md-1">
            <h4 class="mb-4 fw-800 text-white display-6">Shipping Details</h4>
            
            <form id="paymentForm" class="product-card p-4 border-0">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label text-dim small fw-600 text-uppercase">Full Name</label>
                        <input type="text" id="full-name" class="form-control bg-dark bg-opacity-25 border-secondary border-opacity-25 text-white py-3 rounded-3 shadow-none focus-ring" value="<?php echo htmlspecialchars($cust_name); ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-dim small fw-600 text-uppercase">Email Address</label>
                        <input type="email" id="email-address" class="form-control bg-dark bg-opacity-25 border-secondary border-opacity-25 text-white py-3 rounded-3 shadow-none" value="<?php echo htmlspecialchars($cust_email); ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-dim small fw-600 text-uppercase">Delivery Address</label>
                        <textarea id="delivery-address" class="form-control bg-dark bg-opacity-25 border-secondary border-opacity-25 text-white py-3 rounded-3 shadow-none" rows="3" required><?php echo htmlspecialchars($cust_address); ?></textarea>
                    </div>
                </div>

                <div class="mt-5 mb-4">
                    <h6 class="fw-800 text-white mb-3">Secure Payment</h6>
                    <div class="p-3 rounded-4 border border-primary border-opacity-25 bg-primary bg-opacity-5 d-flex align-items-center">
                        <i class="bi bi-shield-check text-success fs-3 me-3"></i>
                        <div>
                            <label class="text-white fw-600 d-block">Paystack Checkout</label>
                            <small class="text-dim">Encryption secured by 256-bit SSL</small>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-800 shadow-lg" type="button" id="pay-button" onclick="payWithPaystack()">
                    <i class="bi bi-credit-card-2-front me-2"></i> PAY ₦<?php echo number_format($total, 2); ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function payWithPaystack() {
    const btn = document.getElementById('pay-button');
    const email = document.getElementById('email-address').value;
    const name = document.getElementById('full-name').value;
    const address = document.getElementById('delivery-address').value;
    const amount = <?php echo $total; ?>;

    if(!email || !name || !address) {
        alert("Please complete all shipping details.");
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> SECURING CONNECTION...';

    let handler = PaystackPop.setup({
        key: '<?php echo $paystack_pk; ?>', // Dynamic Key from your new account
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
            // Success! Send to process_order.php
            window.location.href = "process_order.php?reference=" + response.reference + "&name=" + encodeURIComponent(name) + "&email=" + encodeURIComponent(email);
        },
        onClose: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-credit-card-2-front me-2"></i> RETRY PAYMENT';
        }
    });
    handler.openIframe();
}
</script>