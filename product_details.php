<?php 
require_once 'includes/db.php'; 

// Use your smart session start to avoid those notices!
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) { die("Product not found!"); }

$v_stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ?");
$v_stmt->execute([$product_id]);
$variants = $v_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> | Style Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<div class="ambient-glow">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
</div>

<div class="container py-5">
    <a href="shop.php" class="btn btn-link text-white text-decoration-none mb-4 p-0">
        <i class="bi bi-arrow-left"></i> Back to Shop
    </a>
    
    <div class="product-card p-4 reveal active mx-auto" style="max-width: 900px;">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="rounded-4 overflow-hidden shadow-lg">
                    <img src="assets/images/products/<?php echo $product['image_url']; ?>" 
                         class="img-fluid w-100" alt="Product Image" 
                         style="object-fit: cover; min-height: 400px;">
                </div>
            </div>

            <div class="col-md-6 d-flex flex-column">
                <span class="text-hub-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 2px;">
                    <?php echo $product['cat_name']; ?>
                </span>
                
                <h1 class="fw-800 display-6 mb-2 text-white"><?php echo $product['name']; ?></h1>
                
                <h3 class="text-primary fw-800 mb-4">₦<?php echo number_format($product['base_price'], 2); ?></h3>
                
                <p class="text-dim opacity-75 mb-4"><?php echo nl2br($product['description']); ?></p>
                
                <hr class="border-secondary opacity-25 mb-4">

                <form action="cart_action.php" method="POST" class="mt-auto">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="mb-4">
                        <label class="form-label text-white small fw-600 mb-3">Select Choice (Size/Volume):</label>
                        <div class="row g-2">
                            <?php foreach ($variants as $v): ?>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="selected_variant" 
                                           id="v_<?php echo $v['id']; ?>" 
                                           value="<?php echo $v['attribute_value']; ?>" 
                                           required <?php echo ($v['stock_qty'] <= 0) ? 'disabled' : ''; ?>>
                                    <label class="btn btn-outline-primary w-100 py-2 rounded-3 border-opacity-25" for="v_<?php echo $v['id']; ?>">
                                        <?php echo $v['attribute_value']; ?> 
                                        <small class="d-block opacity-50" style="font-size: 0.65rem;">
                                            <?php echo ($v['stock_qty'] > 0) ? $v['stock_qty'].' left' : 'Sold Out'; ?>
                                        </small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-white small fw-600">Quantity:</label>
                        <input type="number" name="qty" class="form-control bg-dark border-0 text-white" 
                               value="1" min="1" style="width: 100px; border-radius: 10px;">
                    </div>

                    <?php if($product['stock_qty'] > 0): ?>
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold transition-all hover-fill shadow-lg">
                            <i class="bi bi-bag-plus me-2"></i> Add to Cart
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-lg w-100 rounded-pill py-3" disabled>Out of Stock</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>