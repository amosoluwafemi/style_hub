<?php 
require_once '../includes/db.php'; 
require_once 'auth_check.php'; 

// Handle Status Updates via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $update_stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->execute([$new_status, $order_id]);
    header("Location: view_orders.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .status-select { min-width: 130px; font-size: 0.8rem; font-weight: 800; border-radius: 50px; cursor: pointer; transition: 0.2s; }
        .status-select:hover { filter: brightness(1.1); }
        .table-hover tbody tr:hover { background-color: rgba(0,0,0,.02); transition: 0.3s; }
        .cust-name { color: #2c3e50; letter-spacing: -0.3px; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
        <div>
            <h2 class="fw-bold mb-0">Customer Orders</h2>
            <p class="text-muted small">Manage logistics and fulfillment status.</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2 rounded-pill px-4">Dashboard</a>
            <a href="add_product.php" class="btn btn-primary rounded-pill px-4">Add New Product</a>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mx-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Order status updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mx-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4 py-3">Order ID</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Shipping Status</th>
                            <th class="text-center py-3 pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // THE FIX: Using JOIN to connect orders with customers
                        $query = "SELECT orders.*, customers.full_name, customers.email 
                                  FROM orders 
                                  JOIN customers ON orders.customer_id = customers.id 
                                  ORDER BY orders.created_at DESC";
                        
                        $stmt = $pdo->query($query);
                        
                        while ($order = $stmt->fetch()):
                            $status = strtolower($order['status'] ?? 'pending');
                            
                            // Dynamic UI Mapping
                            $statusUI = [
                                'pending'   => 'bg-warning text-dark',
                                'paid'      => 'bg-info text-white',
                                'shipped'   => 'bg-primary text-white',
                                'delivered' => 'bg-success text-white'
                            ];
                            $bgClass = $statusUI[$status] ?? 'bg-secondary text-white';
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#<?php echo $order['id']; ?></td>
                            <td>
                                <div class="fw-bold cust-name"><?php echo htmlspecialchars($order['full_name'] ?? 'N/A'); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($order['email'] ?? ''); ?></div>
                            </td>
                            <td class="fw-bold">₦<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td class="small"><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm status-select <?php echo $bgClass; ?>">
                                        <option value="pending" <?php if($status == 'pending') echo 'selected'; ?>>PENDING</option>
                                        <option value="paid" <?php if($status == 'paid') echo 'selected'; ?>>PAID</option>
                                        <option value="shipped" <?php if($status == 'shipped') echo 'selected'; ?>>SHIPPED</option>
                                        <option value="delivered" <?php if($status == 'delivered') echo 'selected'; ?>>DELIVERED</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                    <a href="print_invoice.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn btn-sm btn-dark px-3" title="Print Invoice">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-dark px-3" title="View Details">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>