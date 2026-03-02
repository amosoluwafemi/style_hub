<?php 
require_once '../includes/db.php'; 
require_once 'auth_check.php'; 

// 1. Handle the Deletion Logic
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    try {
        $pdo->beginTransaction();

        // A. Get image name to delete file
        $img_stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
        $img_stmt->execute([$id]);
        $image_name = $img_stmt->fetchColumn();

        if ($image_name) {
            $file_path = "../assets/images/products/" . $image_name;
            if (file_exists($file_path)) { unlink($file_path); }
        }

        // B. Delete variants first
        $delete_variants = $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?");
        $delete_variants->execute([$id]);

        // C. Delete the product
        $delete_stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delete_stmt->execute([$id]);

        $pdo->commit();
        header("Location: manage_products.php?msg=Product and variants deleted successfully");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error deleting product: " . $e->getMessage());
    }
}

// 2. Fetch all products with calculated variant stock
$products = $pdo->query("SELECT p.*, c.name as cat_name, 
                         (SELECT SUM(stock_qty) FROM product_variants WHERE product_id = p.id) as total_variant_stock 
                         FROM products p 
                         JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Inventory | Style Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Management</h2>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2">Dashboard</a>
            <a href="add_product.php" class="btn btn-primary">Add New Product</a>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo $_GET['msg']; ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td>
                            <img src="../assets/images/products/<?php echo $p['image_url']; ?>" width="60" class="rounded shadow-sm">
                        </td>
                        <td><strong><?php echo $p['name']; ?></strong></td>
                        <td><span class="badge bg-secondary"><?php echo ucfirst($p['cat_name']); ?></span></td>
                        <td>₦<?php echo number_format($p['base_price'], 2); ?></td>
                        <td>
                            <?php 
                            // Determine display stock: if total_variant_stock is NULL (no variants), use base stock
                            $display_stock = ($p['total_variant_stock'] !== null) ? $p['total_variant_stock'] : $p['stock_qty'];
                            
                            if($display_stock <= 5): ?>
                                <span class="badge bg-danger">Low: <?php echo $display_stock; ?></span>
                            <?php else: ?>
                                <span class="badge bg-success"><?php echo $display_stock; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="manage_products.php?delete_id=<?php echo $p['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this product? All related variants will also be removed.')">
                                   Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>