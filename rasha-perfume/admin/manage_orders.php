<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../../index.php?error=unauthorized');
    exit;
}


$stmt = $pdo->query("
    SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$processingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")->fetchColumn();
$shippedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'shipped'")->fetchColumn();
$deliveredOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn();
$cancelledOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();

$message = '';
$error = '';

if (isset($_GET['success'])) {
    $message = 'Order status updated successfully!';
}
if (isset($_GET['error'])) {
    $error = 'Failed to update order status. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Rasha Perfume Admin</title>
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
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="manage_products.php" class="nav-link">Products</a>
                <a href="manage_orders.php" class="nav-link active">Orders</a>
                <a href="../php/logout.php" class="btn-auth">Logout</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-shopping-cart"></i> Manage Orders</h1>
            </div>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <h3><?php echo $totalOrders; ?></h3>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card" style="background: #ff9800;">
                    <i class="fas fa-clock"></i>
                    <h3><?php echo $pendingOrders; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="stat-card" style="background: #2196f3;">
                    <i class="fas fa-cogs"></i>
                    <h3><?php echo $processingOrders; ?></h3>
                    <p>Processing</p>
                </div>
                <div class="stat-card" style="background: #9c27b0;">
                    <i class="fas fa-truck"></i>
                    <h3><?php echo $shippedOrders; ?></h3>
                    <p>Shipped</p>
                </div>
                <div class="stat-card" style="background: #4caf50;">
                    <i class="fas fa-check-circle"></i>
                    <h3><?php echo $deliveredOrders; ?></h3>
                    <p>Delivered</p>
                </div>
                <div class="stat-card" style="background: #f44336;">
                    <i class="fas fa-times-circle"></i>
                    <h3><?php echo $cancelledOrders; ?></h3>
                    <p>Cancelled</p>
                </div>
            </div>

            
            <div class="orders-table-container">
                <h2>All Orders</h2>
                
                <?php if(empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <p>No orders found</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                                <tr class="order-row" data-status="<?php echo $order['status']; ?>">
                                    <td>
                                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="order-link">
                                            #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <form method="POST" action="update_order_status.php" class="status-update-form">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <input type="hidden" name="return_page" value="manage_orders.php">
                                            <select name="status" class="status-badge status-<?php echo $order['status']; ?>" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['phone']); ?></td>
                                    <td><?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?></td>
                                    <td class="action-buttons">
                                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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