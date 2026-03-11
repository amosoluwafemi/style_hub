<?php 
require_once 'includes/db.php'; 
require_once 'includes/header.php'; 

$token = $_GET['token'] ?? '';
$isValid = false;

if ($token) {
    // 1. Fetch the user and the expiry timestamp
    $stmt = $pdo->prepare("SELECT id, token_expiry FROM customers WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Manual Time Check: Compare current time to expiry time
        $current_time = time();
        $expiry_time = strtotime($user['token_expiry']);

        if ($expiry_time > $current_time) {
            $isValid = true;
        } else {
            // Link actually expired
            $error_reason = "This link expired at " . date('H:i', $expiry_time);
        }
    }
}
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-md-5">
        <div class="card p-5 shadow-lg border-0 text-center">
            <?php if ($isValid): ?>
                <i class="bi bi-shield-check text-primary display-4"></i>
                <h2 class="fw-800 mt-3">Reset Password</h2>
                <p class="text-white-50 small mb-4">You are now verified. Enter your new password below.</p>

                <form action="update_password_logic.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">
                        Update Password
                    </button>
                </form>
            <?php else: ?>
                <i class="bi bi-exclamation-octagon text-danger display-4"></i>
                <h2 class="fw-800 mt-3">Link Invalid</h2>
                <p class="text-white-50 small mb-4"><?php echo $error_reason ?? "We couldn't find a valid reset request for this link."; ?></p>
                <a href="forgot_password.php" class="btn btn-outline-light rounded-pill px-4">Try Again</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>