<?php 
require_once 'includes/db.php'; 
require_once 'includes/header.php'; 
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-md-5 reveal"> <div class="card p-4 p-md-5 shadow-lg border-0">
            <div class="text-center mb-4 reveal delay-1">
                <h2 class="fw-800 product-name">Welcome Back</h2>
                <p class="text-dark-50 small">Login to manage your orders and profile.</p>
            </div>

            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success border-0 small mb-4 reveal">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                </div>
            <?php endif; ?>

            <form action="login_logic.php" method="POST" class="reveal delay-2">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase opacity-75">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="yourname@mail.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase ">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="text-end mt-2">
                        <a href="forgot_password.php" class="text-primary small text-decoration-none fw-bold">Forgot Password?</a>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-3">
                    Sign In
                </button>
                
                <div class="text-center mt-3">
                    <p class="small text-dark-50">Don't have an account? 
                        <a href="register.php" class="text-dark fw-bold text-decoration-none">Create One</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>