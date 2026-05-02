<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../index.php?error=unauthorized');
    exit;
}

$order_id = $_GET['id'] ?? 0;


$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: manage_orders.php?error=order_not_found');
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
    <title>Order Details - Rasha Perfume Admin</title>
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
                <a href="../../index.php" class="nav-link">View Store</a>
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="manage_products.php" class="nav-link">Products</a>
                <a href="manage_orders.php" class="nav-link">Orders</a>
                <a href="../php/logout.php" class="btn-auth">Logout</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="page-header">
                <a href="manage_orders.php" class="btn-secondary">&larr; Back to Orders</a>
                <h1>Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h1>
            </div>

            <div class="order-details-grid">
               
                <div class="order-info-card">
                    <h3><i class="fas fa-info-circle"></i> Order Information</h3>
                    <div class="info-row">
                        <span class="label">Order Date:</span>
                        <span class="value"><?php echo date('F d, Y g:i A', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Order Status:</span>
                        <span class="value status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Total Amount:</span>
                        <span class="value total">$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>

                
                <div class="customer-info-card">
                    <h3><i class="fas fa-user"></i> Customer Information</h3>
                    <div class="info-row">
                        <span class="label">Name:</span>
                        <span class="value"><?php echo htmlspecialchars($order['user_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Email:</span>
                        <span class="value"><?php echo htmlspecialchars($order['user_email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Phone:</span>
                        <span class="value"><?php echo htmlspecialchars($order['phone']); ?></span>
                    </div>
                </div>

                
                <div class="shipping-info-card">
                    <h3><i class="fas fa-truck"></i> Shipping Information</h3>
                    <div class="info-row">
                        <span class="label">Address:</span>
                        <span class="value"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                    </div>
                </div>

                
                <div class="order-items-card">
                    <h3><i class="fas fa-boxes"></i> Order Items</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($order_items as $item): ?>
                                <tr>
                                    <td class="product-cell">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-thumb">
                                        <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    </td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3" class="total-label"><strong>Total:</strong></td>
                                <td class="total-amount"><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            
            <div class="update-status-card">
                <h3><i class="fas fa-edit"></i> Update Order Status</h3>
                <form method="POST" action="update_order_status.php" class="update-status-form">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <input type="hidden" name="return_page" value="order_details.php?id=<?php echo $order['id']; ?>">
                    <div class="form-group">
                        <select name="status" class="status-select">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Update Status</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Rasha Perfume. All rights reserved.</p>
        </div>
    </footer>

    <script src="../js/admin.js"></script>
</body>
</html>