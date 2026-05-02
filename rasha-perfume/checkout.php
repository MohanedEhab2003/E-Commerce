<?php
require_once 'php/config.php';
require_once 'php/config.php';
requireActiveUser();

if (!isLoggedIn()) {
    header('Location: login.php?error=please_login');
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) {
    header('Location: cart.php?error=cart_empty');
    exit;
}


$stmt = $pdo->prepare("SELECT phone, address FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$saved_phone = $user['phone'] ?? '';
$saved_address = $user['address'] ?? '';


$productIds = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($productIds);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($products as $product) {
    $total += $product['price'] * $cart[$product['id']];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Rasha Perfume</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/checkout.css">
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
                </a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <h1 class="page-title">Checkout</h1>
            
            <div class="checkout-wrapper">
                <form method="POST" action="php/place_order.php" id="checkoutForm">
                    <div class="checkout-grid">
                        
                        <div class="shipping-section">
                            <h2><i class="fas fa-truck"></i> Shipping Information</h2>
                            
                            <?php if(!empty($saved_address) || !empty($saved_phone)): ?>
                                <div class="saved-info-notice">
                                    <i class="fas fa-info-circle"></i> 
                                    We've pre-filled your saved information. You can update it below.
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" readonly class="readonly-field">
                            </div>
                            
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" readonly class="readonly-field">
                            </div>
                            
                            <div class="form-group">
                                <label>Phone Number *</label>
                                <input type="tel" name="phone" required placeholder="Your phone number" 
                                       value="<?php echo htmlspecialchars($saved_phone); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Shipping Address *</label>
                                <textarea name="shipping_address" rows="3" required placeholder="Street address, city, postal code, country"><?php echo htmlspecialchars($saved_address); ?></textarea>
                            </div>
                            
                            <div class="form-note">
                                <i class="fas fa-save"></i> 
                                Your phone and address will be saved to your account for future orders.
                            </div>
                        </div>

                        
                        <div class="payment-section">
                            <h2><i class="fas fa-credit-card"></i> Payment Information</h2>
                            
                            <div class="payment-methods">
                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="credit_card" checked data-payment-type="card">
                                    <i class="fab fa-cc-visa"></i>
                                    <i class="fab fa-cc-mastercard"></i>
                                    <i class="fab fa-cc-amex"></i>
                                    <span>Credit Card</span>
                                </label>
                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="debit_card" data-payment-type="card">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Debit Card</span>
                                </label>
                            </div>

                            <div id="cardDetails" class="card-details">
                                <div class="form-group">
                                    <label>Card Number *</label>
                                    <div class="card-input-wrapper">
                                        <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                        <i class="fas fa-credit-card card-icon"></i>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Card Holder Name *</label>
                                        <input type="text" name="card_holder_name" id="card_holder_name" placeholder="Name on card">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Expiry Date *</label>
                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY" maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label>CVV *</label>
                                        <input type="password" name="cvv" id="cvv" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>

                            <div class="payment-security">
                                <i class="fas fa-lock"></i>
                                <span>Your payment information is secure and encrypted</span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="order-summary">
                        <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                        <div class="summary-items">
                            <?php foreach($products as $product): ?>
                                <div class="summary-item">
                                    <div class="item-info">
                                        <span class="item-name"><?php echo htmlspecialchars($product['name']); ?></span>
                                        <span class="item-quantity">x <?php echo $cart[$product['id']]; ?></span>
                                    </div>
                                    <span class="item-price">$<?php echo number_format($product['price'] * $cart[$product['id']], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="summary-total">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="summary-total">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-grand-total">
                            <strong>Total:</strong>
                            <strong>$<?php echo number_format($total, 2); ?></strong>
                        </div>
                        
                        <button type="submit" class="btn-primary btn-block" id="placeOrderBtn">
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Rasha Perfume. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/checkout.js"></script>
</body>
</html>