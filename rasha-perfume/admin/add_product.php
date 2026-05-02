<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../index.php?error=unauthorized');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?section=add_product');
    exit;
}

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? 0;
$category = $_POST['category'] ?? '';
$stock = $_POST['stock'] ?? 0;
$image_url = $_POST['image_url'] ?? '';

if (empty($name) || empty($description) || $price <= 0 || empty($category) || $stock < 0) {
    header('Location: index.php?section=add_product&error=invalid_data');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$name, $description, $price, $category, $stock, $image_url]);
    
    if ($result) {
        header('Location: index.php?section=products&success=product_added');
    } else {
        header('Location: index.php?section=add_product&error=insert_failed');
    }
} catch(PDOException $e) {
    error_log("Add product error: " . $e->getMessage());
    header('Location: index.php?section=add_product&error=database_error');
}
exit;
?>