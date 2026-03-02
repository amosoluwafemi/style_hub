<?php
require_once '../includes/db.php';

// Set your desired password here
$new_password = 'admin123'; 
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
    $stmt->execute([$new_hash]);
    
    echo "<h3>Success!</h3>";
    echo "The password for 'admin' has been reset to: <b>$new_password</b>";
    echo "<br><br><a href='login.php'>Go to Login</a>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>