<?php
// 1. Manually include the PHPMailer files
require 'vendor/src/Exception.php';
require 'vendor/src/PHPMailer.php';
require 'vendor/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // 2. CHANGED 'name' to 'full_name' to match your DB screenshot
    $stmt = $pdo->prepare("SELECT id, full_name FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // 3. Update the database
        $update = $pdo->prepare("UPDATE customers SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $update->execute([$token, $expiry, $email]);

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'amosoluwafemi7@gmail.com'; // REPLACE THIS
            $mail->Password   = 'zxfl xntn mzer wtxb';   // REPLACE THIS WITH 16-CHAR KEY
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients - Using 'full_name' here too
            $mail->setFrom('amosoluwafemi7@gmail.com', 'Style Hub Luxury');
            $mail->addAddress($email, $user['full_name']);

            // Content
            $resetLink = "http://localhost/style_hub/reset_password.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - Style Hub';
            $mail->Body    = "
                <div style='font-family: Arial; padding: 20px; border: 1px solid #eee;'>
                    <h2>Hello, {$user['full_name']}</h2>
                    <p>You requested a password reset for your Style Hub account.</p>
                    <a href='$resetLink' style='background: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 50px;'>Reset My Password</a>
                    <p>This link expires in 1 hour.</p>
                </div>";

            $mail->send();
            $_SESSION['success_msg'] = "Check your inbox! A reset link has been sent.";
        } catch (Exception $e) {
            $_SESSION['error_msg'] = "Mail could not be sent. Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error_msg'] = "No account found with that email.";
    }
    header("Location: forgot_password.php");
    exit();
}