<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// 1. Protection: If not logged in, send to login page
if (!$loggedInId) {
    header("Location: login.php");
    exit();
}

$message = "";

// 2. Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $update_stmt = $pdo->prepare("UPDATE customers SET full_name = ?, email = ?, address = ? WHERE id = ?");
    if ($update_stmt->execute([$full_name, $email, $address, $loggedInId])) {
        $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
        // Refresh session name if changed
        $_SESSION['customer_name'] = $full_name;
    } else {
        $message = "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";
    }
}

// 3. Fetch Current Data
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$loggedInId]);
$user = $stmt->fetch();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-4 border-0">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>My Profile</h4>
                    <p class="small mb-0 opacity-75">Manage your personal information and delivery address</p>
                </div>
                
                <div class="card-body p-4 p-lg-5 bg-dark">
                    <?php echo $message; ?>

                    <form action="profile.php" method="POST">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="full_name" class="form-control border-start-0 ps-0 py-2" 
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0 py-2" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Delivery Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 align-items-start pt-2">
                                        <i class="bi bi-geo-alt text-muted"></i>
                                    </span>
                                    <textarea name="address" class="form-control border-start-0 ps-0" rows="3" 
                                              placeholder="Street name, City, State" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                            </div>

                            <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="orders.php" class="btn btn-light rounded-pill px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted small">Looking for your purchases? <a href="orders.php" class="text-primary fw-bold text-decoration-none">View Order History</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>