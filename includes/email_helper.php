<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

function sendOrderReceipt($customerEmail, $customerName, $orderId, $totalAmount) {
    $mail = new PHPMailer(true);

    try {
        // Server settings (Using Gmail as an example)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@gmail.com'; // Your email
        $mail->Password   = 'your-app-password';   // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('sales@stylehub.com', 'Style Hub');
        $mail->addAddress($customerEmail, $customerName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation #$orderId - Style Hub";
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px;'>
                <h2 style='color: #0d6efd;'>Thank you for your order, $customerName!</h2>
                <p>We've received your order <strong>#$orderId</strong> and are currently processing it.</p>
                <hr>
                <h3>Order Summary:</h3>
                <p style='font-size: 18px;'>Total Amount: <strong>₦" . number_format($totalAmount, 2) . "</strong></p>
                <p>We will notify you once your items are out for delivery.</p>
                <br>
                <p>Stay Stylish,<br>The Style Hub Team</p>
            </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}