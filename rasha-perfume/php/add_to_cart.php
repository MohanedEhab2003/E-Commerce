<?php
require_once 'config.php';
require_once 'php/config.php';
requireActiveUser(); 

if (!isLoggedIn()) {
    header('Location: ../login.php?error=please_login');
    exit;
}

$product_id = $_POST['product_id'] ?? '';
$quantity = (int)($_POST['quantity'] ?? 1);

if (empty($product_id)) {
    header('Location: ../index.php?error=invalid_product');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

header('Location: ../index.php?success=added_to_cart');
?>