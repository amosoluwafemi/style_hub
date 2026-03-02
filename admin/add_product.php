<?php 
require_once '../includes/db.php'; 
require_once 'auth_check.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Style Hub | Admin Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Add New Product</h4>
                </div>
                <div class="card-body">
                    <form action="process_product.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" id="catSelect" onchange="updateSizes()" required>
                                    <option value="">Choose...</option>
                                    <option value="wears">Wears (Men/Women/Kids)</option>
                                    <option value="shoes">Footwear</option>
                                    <option value="skincare">Skincare</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (₦)</label>
                                <input type="number" name="price" class="form-control" step="0.01" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3 p-3 border rounded bg-white" id="sizeSection">
                            <label class="form-label d-block"><strong>Select Sizes & Enter Stock:</strong></label>
                            <div id="sizeOptions">
                                <span class="text-muted">Please select a category first...</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="product_image" class="form-control" accept="image/*" required>
                        </div>

                        <button type="submit" name="submit" class="btn btn-success w-100">Upload Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateSizes() {
    const cat = document.getElementById('catSelect').value;
    const container = document.getElementById('sizeOptions');
    container.innerHTML = '';

    let options = [];
    if (cat === 'wears') options = ['S', 'M', 'L', 'XL', 'XXL'];
    if (cat === 'shoes') options = ['38', '39', '40', '41', '42', '43', '44'];
    if (cat === 'skincare') options = ['50ml', '100ml', '250ml', '500ml'];

    options.forEach(opt => {
        // Create a layout with variant checkbox and a quantity input
        container.innerHTML += `
            <div class="row align-items-center mb-2">
                <div class="col-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="variants[]" value="${opt}" id="chk_${opt}" onchange="toggleInput('${opt}')">
                        <label class="form-check-label" for="chk_${opt}">${opt}</label>
                    </div>
                </div>
                <div class="col-8">
                    <input type="number" name="qty_${opt}" id="qty_${opt}" class="form-control form-control-sm" placeholder="Stock Qty" min="1" disabled>
                </div>
            </div>`;
    });
}

function toggleInput(opt) {
    const chk = document.getElementById('chk_' + opt);
    const qty = document.getElementById('qty_' + opt);
    qty.disabled = !chk.checked;
    if(chk.checked) qty.required = true;
}
</script>

</body>
</html>