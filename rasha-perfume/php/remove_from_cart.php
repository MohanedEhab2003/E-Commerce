<?php
require_once 'config.php';

$product_id = $_GET['product_id'] ?? '';

if ($product_id && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

header('Location: ../cart.php');
?>