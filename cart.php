<?php 
require_once 'includes/db.php'; 
require_once 'includes/header.php'; 

// FIX 1: Define the variable early so it exists even if the cart is empty
$total = 0; 
?>

<style>
    .cart-img-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.1);
    }
</style>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark text-white">
                    <div class="table-responsive">
                        <table class="table align-middle table-dark mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="ps-4">PRODUCT</th>
                                    <th>PRICE</th>
                                    <th>QTY</th>
                                    <th>SUBTOTAL</th>
                                    <th class="text-center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($_SESSION['cart'] as $id => $item): 
                                        // FIX 2: Check for both 'qty' and 'quantity' to avoid key errors
                                        $current_qty = $item['qty'] ?? $item['quantity'] ?? 1;
                                        $subtotal = $item['price'] * $current_qty;
                                        $total += $subtotal;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center py-2">
                                            <img src="assets/images/products/<?php echo $item['image']; ?>" class="cart-img-thumb me-3">
                                            <span class="fw-bold"><?php echo $item['name']; ?></span>
                                        </div>
                                    </td>
                                    <td>₦<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $current_qty; ?></td>
                                    <td class="text-primary fw-bold">₦<?php echo number_format($subtotal, 2); ?></td>
                                    <td class="text-center">
                                        <a href="remove_item.php?id=<?php echo $id; ?>" class="text-danger"><i class="bi bi-trash3"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-dark rounded-4 border border-secondary">
                    <h4 class="text-white">Your cart is empty</h4>
                    <a href="shop.php" class="btn btn-primary rounded-pill mt-3">Go to Shop</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 p-3 bg-dark text-white">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Items Total:</span>
                        <span>₦<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Delivery:</span>
                        <span class="text-success fw-bold">Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold">Grand Total:</span>
                        <span class="h5 fw-bold text-primary">₦<?php echo number_format($total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-primary btn-lg w-100 rounded-pill <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>">
                        Checkout Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>