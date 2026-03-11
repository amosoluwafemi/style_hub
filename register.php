<?php 
require_once 'includes/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = trim($_POST['address']);

    // Check if email already exists
    $checkEmail = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
    $checkEmail->execute([$email]);
    
    if ($checkEmail->rowCount() > 0) {
        $error = "This email is already registered.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO customers (full_name, email, password, address) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $pass, $address]);
            echo "<script>alert('Account created successfully!'); window.location.href='login.php';</script>";
            exit();
        } catch (PDOException $e) {
            $error = "Registration failed. Please try again.";
        }
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
    <style>
        /* This fix ensures your form is VISIBLE even if 'reveal' logic fails */
        .reveal { opacity: 1 !important; visibility: visible !important; transform: none !important; }
        
        body { background: #0f0f0f; color: #fff; }
        .card { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 20px;
        }
        .form-control {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .form-control:focus {
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            border-color: #0d6efd;
            box-shadow: none;
        }
        label { color: rgba(255, 255, 255, 0.7); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="col-md-5">
            <div class="card p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-white">Create Account</h2>
                    <p class="text-white">Join Style Hub for the best in fashion & skincare</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold uppercase">Full Name</label>
                        <input type="text" name="full_name" class="form-control py-2" placeholder="Enter your full name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold uppercase">Email Address</label>
                        <input type="email" name="email" class="form-control py-2" placeholder="name@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold uppercase">Password</label>
                        <input type="password" name="password" class="form-control py-2" placeholder="Create a password" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold uppercase">Shipping Address</label>
                        <textarea name="address" class="form-control py-2" rows="3" placeholder="Where should we deliver your orders?"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold">SIGN UP</button>
                    
                    <div class="text-center mt-4">
                        <p class="small text-white">Already have an account? <a href="login.php" class="text-white fw-bold text-decoration-none">Login here</a></p>
                    </div>
                </form>
            </div>
            
            <div class="text-center mt-3">
                <a href="index.php" class="text-white small text-decoration-none">← Back to Home</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>