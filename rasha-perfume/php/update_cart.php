<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cart.php');
    exit;
}

$product_id = $_POST['product_id'] ?? '';
$quantity = (int)($_POST['quantity'] ?? 0);

if ($quantity <= 0) {
    unset($_SESSION['cart'][$product_id]);
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

header('Location: ../cart.php');
?>