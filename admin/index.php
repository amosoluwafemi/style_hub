<?php 
require_once '../includes/db.php'; 
require_once 'auth_check.php'; 

// 1. Fetch Stats
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?? 0;

// 2. Fetch Latest 5 Orders
$latest_orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();

// 3. Inventory Alert Logic (Threshold of 5 units)
$low_stock_count = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_qty <= 5")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Style Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card-stat { transition: transform 0.2s; }
        .card-stat:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">STYLE HUB ADMIN</a>
        <div class="d-flex">
            <a href="../index.php" class="btn btn-sm btn-outline-light me-2">View Website</a>
            <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white card-stat">
                <div class="card-body">
                    <h6>Total Revenue</h6>
                    <h2 class="fw-bold">₦<?php echo number_format($total_revenue, 2); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white card-stat">
                <div class="card-body">
                    <h6>Total Orders</h6>
                    <h2 class="fw-bold"><?php echo $order_count; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-dark text-white card-stat">
                <div class="card-body">
                    <h6>Total Products</h6>
                    <h2 class="fw-bold"><?php echo $product_count; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="list-group shadow-sm">
                <h5 class="list-group-item list-group-item-action active bg-primary border-primary">Quick Actions</h5>
                
                <a href="low_stock_report.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Inventory Alerts</span>
                    <?php if($low_stock_count > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?php echo $low_stock_count; ?></span>
                    <?php endif; ?>
                </a>

                <a href="add_product.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-plus-circle me-2"></i>Add New Product
                </a>
                
                <a href="manage_products.php" class="list-group-item list-group-item-action text-primary fw-bold">
                    <i class="bi bi-box-seam me-2"></i>Manage Products
                </a>
                
                <a href="view_orders.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-cart-check me-2"></i>View All Orders
                </a>
                
                <a href="#" class="list-group-item list-group-item-action text-muted">
                    <i class="bi bi-tags me-2"></i>Manage Categories
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    Recent Orders
                    <a href="view_orders.php" class="btn btn-sm btn-link text-decoration-none p-0">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($latest_orders) > 0): ?>
                                    <?php foreach($latest_orders as $o): ?>
                                    <tr>
                                        <td><span class="text-muted">#</span><?php echo $o['id']; ?></td>
                                        <td class="fw-bold"><?php echo $o['customer_name']; ?></td>
                                        <td>₦<?php echo number_format($o['total_amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>