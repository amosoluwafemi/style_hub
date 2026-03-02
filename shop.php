<?php 
require_once 'includes/db.php'; 
require_once 'includes/header.php'; 
?>

<div class="ambient-glow">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<div class="container mt-5 pt-3" style="position: relative; z-index: 2;">
<div class="container mt-5 pt-3">
    <div class="text-center mb-5 reveal">
        <h1 id="typewriter-h1" class="fw-800 display-4"></h1>
        <p id="typewriter-p" class="text-dim"></p>
    </div>
    
    <div class="d-flex justify-content-center flex-wrap gap-3 mb-5 reveal delay-1">
        <?php 
        $current_cat = $_GET['cat'] ?? ''; 
        $categories = [
            '' => 'All Collections',
            'wears' => 'Wears',
            'shoes' => 'Footwear',
            'skincare' => 'Skincare'
        ];

        foreach ($categories as $slug => $label): 
            $isActive = ($current_cat === $slug);
        ?>
            <a href="shop.php<?php echo $slug ? "?cat=$slug" : ""; ?>" 
               class="btn <?php echo $isActive ? 'btn-primary px-4' : 'btn-outline-light px-4 opacity-75'; ?> rounded-pill border-2 fw-600 transition-all">
               <?php echo $label; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row g-4 mb-5">
        <?php
        $params = [];
        $query = "SELECT p.*, c.name as cat_name FROM products p 
                  JOIN categories c ON p.category_id = c.id WHERE 1=1";

        if (!empty($_GET['cat'])) {
            $query .= " AND c.name = ?";
            $params[] = $_GET['cat'];
        }

        if (!empty($_GET['search'])) {
            $query .= " AND p.name LIKE ?";
            $params[] = "%" . $_GET['search'] . "%";
        }

        $query .= " ORDER BY p.id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        if (count($products) > 0):
            $count = 1;
            foreach ($products as $row):
                $delayClass = ($count <= 8) ? "delay-" . ($count % 4 ?: 4) : "delay-1"; 
        ?>
            <div class="col-lg-3 col-md-4 col-sm-6 reveal <?php echo $delayClass; ?>">
                <div class="card h-100 product-card border-0 overflow-hidden">
                    <div class="product-img-container">
                        <img src="assets/images/products/<?php echo htmlspecialchars($row['image_url']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        
                    </div>
                    
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <span class="text-hub-gold small fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">
                            <?php echo $row['cat_name']; ?>
                        </span>
                        <h5 class="card-title fw-bold mb-2 text-white"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="h5 text-primary fw-800 mt-auto mb-3">₦<?php echo number_format($row['base_price'], 2); ?></p>
                        
                        <a href="product_details.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-outline-primary btn-sm rounded-pill py-2 fw-bold w-100 transition-all hover-fill">
                           View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php 
            $count++;
            endforeach; 
        else:
            echo "<div class='text-center w-100 py-5 reveal'>
                    <i class='bi bi-search display-1 text-muted d-block mb-3'></i>
                    <h4 class='text-muted'>We couldn't find any products matching that.</h4>
                    <a href='shop.php' class='btn btn-primary rounded-pill mt-3 px-5'>Show All Products</a>
                  </div>";
        endif;
        ?>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?>