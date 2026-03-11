<?php 
require_once 'includes/db.php'; 
require_once 'includes/header.php'; 
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-md-5">
        <div class="card border-0 shadow-lg p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="bg-primary d-inline-block p-3 rounded-circle mb-3 shadow">
                    <i class="bi bi-shield-lock text-white h2"></i>
                </div>
                <h2 class="fw-800">Reset Password</h2>
                <p class="text-muted small">Enter your email and we'll send you a link to get back into your account.</p>
            </div>
            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success bg-success text-white border-0 rounded-pill px-4 small shadow-lg">
                    <i class="bi bi-check-circle-fill me-2"></i> 
                    <?php echo $_SESSION['success_msg']; // Remove any 'htmlspecialchars' here so the link works ?>
                    <?php unset($_SESSION['success_msg']); ?>
                </div>
            <?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger bg-danger text-white border-0 rounded-pill px-4 small">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
    </div>
<?php endif; ?>
            <form action="send_reset_logic.php" method="POST">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase opacity-75">Email Address</label>
                    <div class="input-group bg-dark-subtle rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-transparent border-0 ps-3">
                            <i class="bi bi-envelope text-primary"></i>
                        </span>
                        <input type="email" name="email" class="form-control bg-transparent border-0 py-2" placeholder="name@example.com" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm mb-3">
                    Send Reset Link
                </button>
                
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none small text-primary fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>