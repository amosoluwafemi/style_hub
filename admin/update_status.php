<?php
require_once '../includes/db.php';
session_start();

// Basic admin security check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $order_id])) {
        // Redirect back to orders view with a success message
        header("Location: view_orders.php?msg=StatusUpdated");
    } else {
        header("Location: view_orders.php?msg=Error");
    }
}
?>