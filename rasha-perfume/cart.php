<?php
require_once 'php/config.php';
require_once 'php/config.php';
requireActiveUser();

if (!isLoggedIn()) {
    header('Location: login.php?error=please_login');
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartItems = [];
$total = 0;

if (!empty($cart)) {
    $productIds = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $quantity = $cart[$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cartItems[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Rasha Perfume</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php"><i class="fas fa-spray-can"></i> Rasha Perfume</a>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="index.php" class="nav-link">Products</a>
                <a href="cart.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> Cart 
                    <span class="cart-count"><?php echo array_sum($_SESSION['cart'] ?? []); ?></span>
                </a>
                <div class="user-menu">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="php/logout.php" class="btn-auth">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <h1 class="page-title">Your Shopping Cart</h1>
            
            <?php if(empty($cartItems)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                    <a href="index.php" class="btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cartItems as $item): ?>
                                <tr>
                                    <td class="cart-product">
                                        <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                                        <div>
                                            <h4><?php echo htmlspecialchars($item['product']['name']); ?></h4>
                                            <small><?php echo htmlspecialchars($item['product']['category']); ?></small>
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($item['product']['price'], 2); ?></td>
                                    <td>
                                        <form method="POST" action="php/update_cart.php" class="quantity-form">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['stock']; ?>" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td>
                                        <a href="php/remove_from_cart.php?product_id=<?php echo $item['product']['id']; ?>" class="btn-remove" onclick="return confirm('Remove this item?')">Remove</a>
                                    </td>
                                </table>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="cart-total-label"><strong>Total:</strong></td>
                                <td colspan="2"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="cart-actions">
                        <a href="index.php" class="btn-secondary">Continue Shopping</a>
                        <a href="checkout.php" class="btn-primary">Proceed to Checkout</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Rasha Perfume. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>