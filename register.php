<?php 
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = $_POST['address'];

    $stmt = $pdo->prepare("INSERT INTO customers (full_name, email, password, address) VALUES (?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $pass, $address]);
        echo "<script>alert('Account created successfully!'); window.location.href='login.php';</script>";
    } catch (PDOException $e) {
        $error = "This email is already registered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Style Hub | Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body class="bg-dark">

<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="col-md-5">
            <div class="card p-4 p-md-5 reveal">
                <div class="text-center mb-4 reveal delay-1">
                    <h2 class="fw-bold">Create Account</h2>
                    <p class="text-muted">Join Style Hub for the best in fashion & skincare</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger py-2 small text-center reveal"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="reveal delay-2">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Create a password" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Shipping Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Where should we deliver your orders?"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold">Sign Up</button>
                    
                    <div class="text-center mt-4">
                        <p class="small text-muted">Already have an account? <a href="login.php" class="text-white fw-bold text-decoration-none">Login here</a></p>
                    </div>
                </form>
            </div>
            
            <div class="text-center mt-3 reveal delay-3">
                <a href="index.php" class="text-muted small text-decoration-none">← Back to Home</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>