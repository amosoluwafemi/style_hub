<?php
require_once '../includes/db.php';
require_once 'auth_check.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    header("Location: view_orders.php?msg=StatusUpdated");
    exit();
}