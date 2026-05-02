<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../index.php?error=unauthorized');
    exit;
}


$stmt = $pdo->query("
    SELECT p.*, o.id as order_id, o.total_amount, u.name as user_name 
    FROM payments p 
    JOIN orders o ON p.order_id = o.id 
    JOIN users u ON o.user_id = u.id 
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);


$totalRevenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE payment_status = 'completed'")->fetchColumn();
$completedPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'completed'")->fetchColumn();
$pendingPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'pending'")->fetchColumn();
$failedPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'failed'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Rasha Perfume Admin</title>
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
                <a href="payments.php" class="nav-link active">Payments</a>
                <a href="../php/logout.php" class="btn-auth">Logout</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <h1><i class="fas fa-credit-card"></i> Payment Management</h1>
            
            
            <div class="stats-grid">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <i class="fas fa-dollar-sign"></i>
                    <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
                <div class="stat-card" style="background: #4caf50;">
                    <i class="fas fa-check-circle"></i>
                    <h3><?php echo $completedPayments; ?></h3>
                    <p>Completed Payments</p>
                </div>
                <div class="stat-card" style="background: #ff9800;">
                    <i class="fas fa-clock"></i>
                    <h3><?php echo $pendingPayments; ?></h3>
                    <p>Pending Payments</p>
                </div>
                <div class="stat-card" style="background: #f44336;">
                    <i class="fas fa-times-circle"></i>
                    <h3><?php echo $failedPayments; ?></h3>
                    <p>Failed Payments</p>
                </div>
            </div>

          
            <div class="payments-table-container">
                <h2>All Transactions</h2>
                
                <?php if(empty($payments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-credit-card"></i>
                        <p>No payments found</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Card Last4</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                                    <td>
                                        <a href="order_details.php?id=<?php echo $payment['order_id']; ?>">
                                            #<?php echo str_pad($payment['order_id'], 6, '0', STR_PAD_LEFT); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment['user_name']); ?></td>
                                    <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                                    <td><?php echo $payment['card_number_last4'] ? '**** ' . $payment['card_number_last4'] : 'N/A'; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $payment['payment_status']; ?>">
                                            <?php echo ucfirst($payment['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y g:i A', strtotime($payment['payment_date'])); ?></td>
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
</body>
</html>