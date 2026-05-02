<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../../index.php?error=unauthorized');
    exit;
}

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $image_url = $_POST['image_url'] ?? '';
    
    $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category=?, stock=?, image_url=? WHERE id=?");
    $stmt->execute([$name, $description, $price, $category, $stock, $image_url, $id]);
    
    header('Location: index.php?section=products&success=product_updated');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php?section=products&error=product_not_found');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Rasha Perfume Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php"><i class="fas fa-spray-can"></i> Rasha Perfume - Admin</a>
            </div>
            <div class="nav-links">
                <a href="../index.php" class="nav-link">View Store</a>
                <a href="../php/logout.php" class="btn-auth">Logout</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="edit-product-container">
                <h1>Edit Product</h1>
                <form method="POST" class="admin-form" id="editProductForm">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea name="description" id="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price *</label>
                            <input type="number" step="0.01" name="price" id="price" value="<?php echo $product['price']; ?>" required min="0.01">
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select name="category" id="category" required>
                                <option value="Women" <?php echo $product['category'] == 'Women' ? 'selected' : ''; ?>>Women</option>
                                <option value="Men" <?php echo $product['category'] == 'Men' ? 'selected' : ''; ?>>Men</option>
                                <option value="Unisex" <?php echo $product['category'] == 'Unisex' ? 'selected' : ''; ?>>Unisex</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock">Stock *</label>
                            <input type="number" name="stock" id="stock" value="<?php echo $product['stock']; ?>" required min="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_url">Image URL</label>
                        <input type="url" name="image_url" id="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                        <small class="form-help">Current image will be used if left empty</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Product</button>
                        <a href="index.php?section=products" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../js/admin.js"></script>
</body>
</html>