<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../index.php?error=unauthorized');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $status = $_POST['status'] ?? 'pending';
    $return_page = $_POST['return_page'] ?? 'manage_orders.php';
    
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($status, $valid_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        header("Location: $return_page?success=status_updated");
    } else {
        header("Location: $return_page?error=invalid_status");
    }
} else {
    header('Location: manage_orders.php');
}
exit;
?>