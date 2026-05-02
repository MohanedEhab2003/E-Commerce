<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../index.php?error=unauthorized');
    exit;
}
if (!isUserActive()) {
    session_destroy();
    header('Location: ../login.php?error=account_inactive');
    exit;
}

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();


$totalRevenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE payment_status = 'completed'")->fetchColumn();
$totalRevenue = $totalRevenue ? $totalRevenue : 0;
$completedPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'completed'")->fetchColumn();
$pendingPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'pending'")->fetchColumn();
$failedPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'failed'")->fetchColumn();


$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->query("
    SELECT o.*, u.name as user_name, 
           CASE WHEN p.id IS NOT NULL AND p.payment_status = 'completed' THEN 'completed'
                WHEN p.id IS NOT NULL AND p.payment_status = 'pending' THEN 'pending'
                WHEN p.id IS NOT NULL AND p.payment_status = 'failed' THEN 'failed'
                ELSE 'not_paid'
           END as payment_status,
           p.payment_method, p.transaction_id
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    LEFT JOIN payments p ON o.id = p.order_id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Rasha Perfume</title>
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
                <a href="payments.php" class="nav-link">Payments</a>
                <a href="../php/logout.php" class="btn-auth">Logout</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="admin-container">
                <div class="admin-sidebar">
    <a href="?section=dashboard" class="<?php echo $section == 'dashboard' ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i> Dashboard
    </a>
    <a href="?section=products" class="<?php echo $section == 'products' ? 'active' : ''; ?>">
        <i class="fas fa-box"></i> Products
    </a>
    <a href="?section=add_product" class="<?php echo $section == 'add_product' ? 'active' : ''; ?>">
        <i class="fas fa-plus-circle"></i> Add Product
    </a>
    <a href="?section=orders" class="<?php echo $section == 'orders' ? 'active' : ''; ?>">
        <i class="fas fa-shopping-cart"></i> Orders
    </a>
    <a href="manage_users.php">
        <i class="fas fa-users"></i> Users
    </a>
    <a href="payments.php">
        <i class="fas fa-credit-card"></i> Payments
    </a>
</div>

                <div class="admin-content">
                    <?php if($success == 'product_added'): ?>
                        <div class="alert alert-success">✓ Product added successfully!</div>
                    <?php endif; ?>
                    
                    <?php if($success == 'product_updated'): ?>
                        <div class="alert alert-success">✓ Product updated successfully!</div>
                    <?php endif; ?>
                    
                    <?php if($success == 'product_deleted'): ?>
                        <div class="alert alert-success">✓ Product deleted successfully!</div>
                    <?php endif; ?>
                    
                    <?php if($success == 'status_updated'): ?>
                        <div class="alert alert-success">✓ Order status updated successfully!</div>
                    <?php endif; ?>
                    
                    <?php if($error == 'invalid_data'): ?>
                        <div class="alert alert-error">✗ Please fill in all required fields correctly.</div>
                    <?php endif; ?>
                    
                    <?php if($error == 'insert_failed'): ?>
                        <div class="alert alert-error">✗ Failed to add product. Please try again.</div>
                    <?php endif; ?>
                    
                    <?php if($error == 'database_error'): ?>
                        <div class="alert alert-error">✗ Database error. Please check your connection.</div>
                    <?php endif; ?>

                    
                    <?php if($section == 'dashboard'): ?>
                        <div class="dashboard-section">
                            <h2><i class="fas fa-chart-pie"></i> Dashboard Overview</h2>
                            
                            
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <i class="fas fa-box"></i>
                                    <h3><?php echo $totalProducts; ?></h3>
                                    <p>Total Products</p>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h3><?php echo $totalOrders; ?></h3>
                                    <p>Total Orders</p>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-users"></i>
                                    <h3><?php echo $totalUsers; ?></h3>
                                    <p>Total Customers</p>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-clock"></i>
                                    <h3><?php echo $pendingOrders; ?></h3>
                                    <p>Pending Orders</p>
                                </div>
                            </div>
                            
                            
                            <h3 style="margin-top: 30px; margin-bottom: 20px;"><i class="fas fa-credit-card"></i> Payment Statistics</h3>
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
                                    <i class="fas fa-hourglass-half"></i>
                                    <h3><?php echo $pendingPayments; ?></h3>
                                    <p>Pending Payments</p>
                                </div>
                                <div class="stat-card" style="background: #f44336;">
                                    <i class="fas fa-times-circle"></i>
                                    <h3><?php echo $failedPayments; ?></h3>
                                    <p>Failed Payments</p>
                                </div>
                            </div>

                           
                            <h3 style="margin-top: 30px; margin-bottom: 20px;"><i class="fas fa-recent"></i> Recent Orders</h3>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $recentOrders = array_slice($orders, 0, 5);
                                    foreach($recentOrders as $order): 
                                    ?>
                                        <tr>
                                            <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($order['payment_status'] == 'completed'): ?>
                                                    <span class="status-badge" style="background: #4caf50;">Paid</span>
                                                <?php elseif($order['payment_status'] == 'pending'): ?>
                                                    <span class="status-badge" style="background: #ff9800;">Pending</span>
                                                <?php elseif($order['payment_status'] == 'failed'): ?>
                                                    <span class="status-badge" style="background: #f44336;">Failed</span>
                                                <?php else: ?>
                                                    <span class="status-badge" style="background: #999;">Not Paid</span>
                                                <?php endif; ?>
                                             </span>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                            <td>
                                                <a href="?section=orders&order_id=<?php echo $order['id']; ?>" class="btn-edit">View</a>
                                             </span>
                                         </span>
                                    <?php endforeach; ?>
                                    <?php if(empty($recentOrders)): ?>
                                        <tr>
                                            <td colspan="7" style="text-align: center;">No orders found</span>
                                         </span>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            
                            <div style="text-align: center; margin-top: 20px;">
                                <a href="?section=orders" class="btn-primary">View All Orders</a>
                            </div>
                        </div>
                    <?php endif; ?>

                   
                    <?php if($section == 'products'): ?>
                        <div class="products-section">
                            <h2><i class="fas fa-boxes"></i> Manage Products</h2>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['id']; ?></span>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumb">
                                             </span>
                                            <td><?php echo htmlspecialchars($product['name']); ?></span>
                                            <td><?php echo htmlspecialchars($product['category']); ?></span>
                                            <td>$<?php echo number_format($product['price'], 2); ?></span>
                                            <td>
                                                <?php if($product['stock'] <= 5 && $product['stock'] > 0): ?>
                                                    <span style="color: #ff9800; font-weight: bold;"><?php echo $product['stock']; ?> (Low Stock)</span>
                                                <?php elseif($product['stock'] == 0): ?>
                                                    <span style="color: #f44336; font-weight: bold;">Out of Stock</span>
                                                <?php else: ?>
                                                    <?php echo $product['stock']; ?>
                                                <?php endif; ?>
                                             </span>
                                            <td>
                                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-edit">Edit</a>
                                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                                             </span>
                                         </span>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($section == 'add_product'): ?>
                        <div class="add-product-section">
                            <h2><i class="fas fa-plus-circle"></i> Add New Product</h2>
                            <form method="POST" action="add_product.php" class="admin-form" id="addProductForm">
                                <div class="form-group">
                                    <label for="name">Product Name *</label>
                                    <input type="text" name="name" id="name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Description *</label>
                                    <textarea name="description" id="description" rows="5" required></textarea>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="price">Price *</label>
                                        <input type="number" step="0.01" name="price" id="price" required min="0.01">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="category">Category *</label>
                                        <select name="category" id="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Women">Women</option>
                                            <option value="Men">Men</option>
                                            <option value="Unisex">Unisex</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="stock">Stock *</label>
                                        <input type="number" name="stock" id="stock" required min="0">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="image_url">Image URL</label>
                                    <input type="url" name="image_url" id="image_url" placeholder="https://images.unsplash.com/...">
                                    <small class="form-help">Leave empty to use default image. Recommended: Use Unsplash images (300x300)</small>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">Add Product</button>
                                    <a href="?section=products" class="btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($section == 'orders'): ?>
                        <div class="orders-section">
                            <h2><i class="fas fa-shopping-cart"></i> Manage Orders</h2>
                            
                            
                            <div class="order-filters" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                                <a href="?section=orders&filter=all" class="btn-filter <?php echo (!isset($_GET['filter']) || $_GET['filter'] == 'all') ? 'active' : ''; ?>">All Orders</a>
                                <a href="?section=orders&filter=pending" class="btn-filter <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'pending') ? 'active' : ''; ?>">Pending</a>
                                <a href="?section=orders&filter=processing" class="btn-filter <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'processing') ? 'active' : ''; ?>">Processing</a>
                                <a href="?section=orders&filter=shipped" class="btn-filter <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'shipped') ? 'active' : ''; ?>">Shipped</a>
                                <a href="?section=orders&filter=delivered" class="btn-filter <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'delivered') ? 'active' : ''; ?>">Delivered</a>
                                <a href="?section=orders&filter=cancelled" class="btn-filter <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'cancelled') ? 'active' : ''; ?>">Cancelled</a>
                            </div>
                            
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Method</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
                                    $filteredOrders = $orders;
                                    if($filter != 'all') {
                                        $filteredOrders = array_filter($orders, function($order) use ($filter) {
                                            return $order['status'] == $filter;
                                        });
                                    }
                                    foreach($filteredOrders as $order): 
                                    ?>
                                        <tr>
                                            <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                            <td><?php echo htmlspecialchars($order['user_name']); ?></span>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></span>
                                            <td>
                                                <form method="POST" action="update_order_status.php" class="status-update-form">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="return_page" value="index.php?section=orders&filter=<?php echo $filter; ?>">
                                                    <select name="status" class="status-select status-<?php echo $order['status']; ?>" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                </form>
                                             </span>
                                            <td>
                                                <?php if($order['payment_status'] == 'completed'): ?>
                                                    <span class="status-badge" style="background: #4caf50;">Paid</span>
                                                <?php elseif($order['payment_status'] == 'pending'): ?>
                                                    <span class="status-badge" style="background: #ff9800;">Pending</span>
                                                <?php elseif($order['payment_status'] == 'failed'): ?>
                                                    <span class="status-badge" style="background: #f44336;">Failed</span>
                                                <?php else: ?>
                                                    <span class="status-badge" style="background: #999;">Not Paid</span>
                                                <?php endif; ?>
                                             </span>
                                            <td>
                                                <?php 
                                                if(isset($order['payment_method'])) {
                                                    echo ucfirst(str_replace('_', ' ', $order['payment_method']));
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                             </span>
                                            <td><?php echo htmlspecialchars($order['phone']); ?></span>
                                            <td><?php echo htmlspecialchars(substr($order['shipping_address'], 0, 40)); ?>...</span>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                            <td>
                                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-edit">View</a>
                                             </span>
                                         </span>
                                    <?php endforeach; ?>
                                    <?php if(empty($filteredOrders)): ?>
                                        <tr>
                                            <td colspan="10" style="text-align: center;">No orders found</span>
                                         </span>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Rasha Perfume. All rights reserved.</p>
            <p>Admin Dashboard - Manage your perfume store</p>
        </div>
    </footer>

    <script src="../js/admin.js"></script>
</body>
</html>