<?php
require_once '../includes/db.php';

if (isset($_POST['submit'])) {
    $name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $variants = isset($_POST['variants']) ? $_POST['variants'] : [];

    // 1. Handle Image Upload
    $target_dir = "../assets/images/products/";
    $file_name = time() . "_" . basename($_FILES["product_image"]["name"]);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        
        try {
            $pdo->beginTransaction();

            // 2. Insert into 'products' table
            $sql = "INSERT INTO products (name, category_id, description, base_price, image_url, stock_qty) 
                    VALUES (?, (SELECT id FROM categories WHERE name = ? LIMIT 1), ?, ?, ?, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $category, $description, $price, $file_name]);
            
            $product_id = $pdo->lastInsertId();
            $total_stock = 0;

            // 3. Insert Variants with Specific Stock
            if (!empty($variants)) {
                $variant_sql = "INSERT INTO product_variants (product_id, attribute_value, stock_qty) VALUES (?, ?, ?)";
                $variant_stmt = $pdo->prepare($variant_sql);
                
                foreach ($variants as $size) {
                    // Grab the specific quantity for this variant
                    $qty_field = "qty_" . str_replace('.', '_', $size); // handle 50ml dots if any
                    $qty = isset($_POST[$qty_field]) ? (int)$_POST[$qty_field] : 0;
                    
                    $variant_stmt->execute([$product_id, $size, $qty]);
                    $total_stock += $qty;
                }
            }

            // 4. SYNC: Update main product table with total stock
            $sync_sql = "UPDATE products SET stock_qty = ? WHERE id = ?";
            $pdo->prepare($sync_sql)->execute([$total_stock, $product_id]);

            $pdo->commit();
            echo "<script>alert('Product and Inventory added successfully!'); window.location.href='manage_products.php';</script>";

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error saving to database: " . $e->getMessage());
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}