<?php 
require_once '../includes/db.php'; 
require_once 'auth_check.php'; 

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    
    // Update logic
    $sql = "UPDATE products SET name = ?, base_price = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$name, $price, $id]);
    header("Location: manage_products.php?msg=Updated");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="col-md-6 mx-auto card p-4 shadow-sm">
            <h3>Edit Product: <?php echo $product['name']; ?></h3>
            <form method="POST">
                <div class="mb-3">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>">
                </div>
                <div class="mb-3">
                    <label>Price</label>
                    <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $product['base_price']; ?>">
                </div>
                <button type="submit" class="btn btn-success w-100">Update Product</button>
                <a href="manage_products.php" class="btn btn-link w-100 text-muted mt-2">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>