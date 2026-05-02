<?php
require_once 'config.php';
require_once 'php/config.php';
requireActiveUser(); 

if (!isLoggedIn()) {
    header('Location: ../login.php?error=please_login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../checkout.php');
    exit;
}

$address = $_POST['shipping_address'] ?? '';
$phone = $_POST['phone'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'credit_card';
$card_number = $_POST['card_number'] ?? '';
$card_holder_name = $_POST['card_holder_name'] ?? '';
$expiry_date = $_POST['expiry_date'] ?? '';
$cvv = $_POST['cvv'] ?? '';

if (empty($address)) {
    header('Location: ../checkout.php?error=address_required');
    exit;
}


if ($payment_method == 'credit_card' || $payment_method == 'debit_card') {
    if (empty($card_number) || empty($card_holder_name) || empty($expiry_date) || empty($cvv)) {
        header('Location: ../checkout.php?error=payment_details_required');
        exit;
    }
    
   
    $card_number_clean = preg_replace('/\s+/', '', $card_number);
    if (!preg_match('/^\d{13,19}$/', $card_number_clean)) {
        header('Location: ../checkout.php?error=invalid_card_number');
        exit;
    }
    
    
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry_date)) {
        header('Location: ../checkout.php?error=invalid_expiry_date');
        exit;
    }
    
   
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        header('Location: ../checkout.php?error=invalid_cvv');
        exit;
    }
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: ../cart.php?error=cart_empty');
    exit;
}

try {
    $pdo->beginTransaction();
    
   
    $updateUser = $pdo->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
    $updateUser->execute([$phone, $address, $_SESSION['user_id']]);
    
    
    $_SESSION['user_phone'] = $phone;
    $_SESSION['user_address'] = $address;
    
    
    $productIds = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total = 0;
    $items = [];
    foreach ($products as $product) {
        $quantity = $cart[$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        $items[] = [
            'product' => $product,
            'quantity' => $quantity
        ];
    }
    
    // Create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, phone, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $total, $address, $phone, 'pending']);
    $orderId = $pdo->lastInsertId();
    
    // Process payment
    $payment_status = 'completed';
    $transaction_id = 'TXN_' . time() . '_' . rand(1000, 9999);
    $card_last4 = substr(preg_replace('/\s+/', '', $card_number), -4);
    
    // Insert payment record
    $stmt = $pdo->prepare("INSERT INTO payments (order_id, payment_method, card_number_last4, card_holder_name, amount, payment_status, transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$orderId, $payment_method, $card_last4, $card_holder_name, $total, $payment_status, $transaction_id]);
    
    // Create order items and update stock
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->execute([
            $orderId,
            $item['product']['id'],
            $item['quantity'],
            $item['product']['price']
        ]);
        
        $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $updateStock->execute([$item['quantity'], $item['product']['id']]);
    }
    
    
    unset($_SESSION['cart']);
    
    $pdo->commit();
    header('Location: ../order_confirmation.php?order_id=' . $orderId . '&success=order_placed');
    
} catch(Exception $e) {
    $pdo->rollBack();
    error_log("Order placement error: " . $e->getMessage());
    header('Location: ../checkout.php?error=order_failed');
}
?>