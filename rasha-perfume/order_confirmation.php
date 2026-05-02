<?php
require_once 'php/config.php';
require_once 'php/config.php';
requireActiveUser(); 

if (!isLoggedIn()) {
    header('Location: login.php?error=please_login');
    exit;
}

$order_id = $_GET['order_id'] ?? 0;


$stmt = $pdo->prepare("
    SELECT o.*, p.payment_method, p.card_number_last4, p.transaction_id, p.payment_status
    FROM orders o 
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php?error=order_not_found');
    exit;
}


$stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name, p.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Rasha Perfume</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 30px rgba(0,0,0,0.1);
        }
        .success-header {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
            text-align: center;
            padding: 40px;
        }
        .success-header i {
            font-size: 4rem;
            margin-bottom: 15px;
        }
        .success-header h1 {
            margin: 0;
            font-size: 2rem;
        }
        .order-details {
            padding: 30px;
        }
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .info-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
        }
        .info-box h3 {
            margin-bottom: 10px;
            color: #2c1a4d;
            font-size: 1rem;
        }
        .info-box p {
            color: #666;
            margin: 5px 0;
        }
        .payment-details {
            background: #f0f7ff;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .btn-continue {
            display: inline-block;
            padding: 12px 30px;
            background: #2c1a4d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn-continue:hover {
            background: #4a2c7a;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php"><i class="fas fa-spray-can"></i> Rasha Perfume</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="confirmation-container">
            <div class="success-header">
                <i class="fas fa-check-circle"></i>
                <h1>Order Confirmed!</h1>
                <p>Thank you for your purchase</p>
            </div>
            
            <div class="order-details">
                <div class="order-info-grid">
                    <div class="info-box">
                        <h3>Order Number</h3>
                        <p>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></p>
                    </div>
                    <div class="info-box">
                        <h3>Order Date</h3>
                        <p><?php echo date('F d, Y g:i A', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div class="info-box">
                        <h3>Total Amount</h3>
                        <p style="font-size: 1.3rem; font-weight: bold; color: #2c1a4d;">$<?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                </div>

                <?php if(isset($order['payment_method'])): ?>
                <div class="payment-details">
                    <h3>Payment Information</h3>
                    <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                    <?php if($order['card_number_last4']): ?>
                        <p><strong>Card Ending:</strong> **** **** **** <?php echo $order['card_number_last4']; ?></p>
                    <?php endif; ?>
                    <p><strong>Transaction ID:</strong> <?php echo $order['transaction_id']; ?></p>
                    <p><strong>Payment Status:</strong> <span style="color: #4caf50;"><?php echo ucfirst($order['payment_status']); ?></span></p>
                </div>
                <?php endif; ?>

                <h3>Order Items</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 10px 0;">Product</th>
                            <th style="text-align: center; padding: 10px 0;">Quantity</th>
                            <th style="text-align: right; padding: 10px 0;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($order_items as $item): ?>
                            <tr>
                                <td style="padding: 10px 0;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td style="text-align: center; padding: 10px 0;"><?php echo $item['quantity']; ?></td>
                                <td style="text-align: right; padding: 10px 0;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right; padding: 10px 0; font-weight: bold;">Total:</td>
                            <td style="text-align: right; padding: 10px 0; font-weight: bold;">$<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <div style="text-align: center;">
                    <a href="index.php" class="btn-continue">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Rasha Perfume. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>