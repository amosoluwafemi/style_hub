<?php 
require_once '../includes/db.php'; 
require_once 'auth_check.php'; 

// Set the threshold for what you consider "Low Stock"
$threshold = 5;

// Fetch only products below the threshold
$stmt = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.stock_qty <= ? 
                       ORDER BY p.stock_qty ASC");
$stmt->execute([$threshold]);
$low_stock_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Low Stock Report | Style Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Low Stock Alert (Threshold: <?php echo $threshold; ?>)</h2>
        <a href="index.php" class="btn btn-outline-primary">Back to Dashboard</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if (count($low_stock_items) > 0): ?>
                <div class="alert alert-warning">
                    You have <strong><?php echo count($low_stock_items); ?></strong> products running low.
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($low_stock_items as $item): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $item['name']; ?></strong><br>
                                    <small class="text-muted">ID: #<?php echo $item['id']; ?></small>
                                </td>
                                <td><?php echo ucfirst($item['cat_name']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($item['stock_qty'] == 0) ? 'bg-danger' : 'bg-warning text-dark'; ?>">
                                        <?php echo ($item['stock_qty'] == 0) ? 'OUT OF STOCK' : $item['stock_qty'] . ' Left'; ?>
                                    </span>
                                </td>
                                <td>₦<?php echo number_format($item['base_price'], 2); ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-dark">Update Stock</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <h4 class="text-success">Inventory Healthy!</h4>
                    <p class="text-muted">All products are currently above the threshold.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>