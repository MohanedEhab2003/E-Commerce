<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../index.php?error=unauthorized');
    exit;
}


if (isset($_GET['restore_id']) && is_numeric($_GET['restore_id'])) {
    $restore_id = $_GET['restore_id'];
    
    
    if ($restore_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->execute([$restore_id]);
        $success = "User account has been restored successfully.";
    } else {
        $error = "You cannot restore your own account this way!";
    }
    header('Location: manage_users.php');
    exit;
}


if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    
    if ($delete_id != $_SESSION['user_id']) {
        try {
           
            $checkOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
            $checkOrders->execute([$delete_id]);
            $orderCount = $checkOrders->fetchColumn();
            
            if ($orderCount > 0) {
                
                $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
                $stmt->execute([$delete_id]);
                $success = "User has been deactivated (has existing orders). You can restore them anytime.";
            } else {
                
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$delete_id]);
                $success = "User deleted successfully.";
            }
        } catch(PDOException $e) {
            $error = "Cannot delete user. They may have existing orders.";
        }
    } else {
        $error = "You cannot delete your own admin account!";
    }
    header('Location: manage_users.php');
    exit;
}


if (isset($_GET['permanent_delete_id']) && is_numeric($_GET['permanent_delete_id'])) {
    $delete_id = $_GET['permanent_delete_id'];
    
    if ($delete_id != $_SESSION['user_id']) {
       
        $checkOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $checkOrders->execute([$delete_id]);
        $orderCount = $checkOrders->fetchColumn();
        
        if ($orderCount > 0) {
           
            $error = "Cannot permanently delete user with existing orders. Use deactivate instead.";
        } else {
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$delete_id]);
            $success = "User permanently deleted.";
        }
    } else {
        $error = "You cannot delete your own admin account!";
    }
    header('Location: manage_users.php');
    exit;
}


if (isset($_POST['update_status']) && isset($_POST['user_id']) && isset($_POST['status'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $user_id]);
        $success = "User status updated successfully.";
    } else {
        $error = "You cannot change your own status!";
    }
    header('Location: manage_users.php');
    exit;
}


if (isset($_POST['update_role']) && isset($_POST['user_id']) && isset($_POST['is_admin'])) {
    $user_id = $_POST['user_id'];
    $is_admin = $_POST['is_admin'];
    
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->execute([$is_admin, $user_id]);
        $success = "User role updated successfully.";
    } else {
        $error = "You cannot change your own admin role!";
    }
    header('Location: manage_users.php');
    exit;
}


if (isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $address, $user_id]);
        $success = "User information updated successfully.";
    } catch(PDOException $e) {
        $error = "Email already exists or invalid data.";
    }
    header('Location: manage_users.php');
    exit;
}


$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if (!empty($status_filter)) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 1")->fetchColumn();
$activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active' AND is_admin = 0")->fetchColumn();
$inactiveUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'inactive'")->fetchColumn();
$bannedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'banned'")->fetchColumn();


$view_user = null;
if (isset($_GET['view_id']) && is_numeric($_GET['view_id'])) {
    $view_id = $_GET['view_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$view_id]);
    $view_user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($view_user) {
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as order_count, SUM(total_amount) as total_spent FROM orders WHERE user_id = ?");
        $stmt->execute([$view_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        $view_user['order_count'] = $stats['order_count'] ? $stats['order_count'] : 0;
        $view_user['total_spent'] = $stats['total_spent'] ? $stats['total_spent'] : 0;
    }
}


$edit_user = null;
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Rasha Perfume Admin</title>
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
                <a href="manage_users.php" class="nav-link active">Users</a>
                <a href="payments.php" class="nav-link">Payments</a>
                <a href="../php/logout.php" class="btn-auth">Logout</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-users"></i> Manage Users</h1>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
           
            <?php if($view_user): ?>
            <div class="modal-overlay" id="viewModal">
                <div class="modal-container">
                    <div class="modal-header">
                        <h2>User Details</h2>
                        <a href="manage_users.php" class="modal-close">&times;</a>
                    </div>
                    <div class="modal-body">
                        <div class="detail-group">
                            <label>Name:</label>
                            <p><?php echo htmlspecialchars($view_user['name']); ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Email:</label>
                            <p><?php echo htmlspecialchars($view_user['email']); ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Phone:</label>
                            <p><?php echo !empty($view_user['phone']) ? htmlspecialchars($view_user['phone']) : 'Not provided'; ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Address:</label>
                            <p><?php echo !empty($view_user['address']) ? nl2br(htmlspecialchars($view_user['address'])) : 'Not provided'; ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Role:</label>
                            <p><?php echo $view_user['is_admin'] ? 'Administrator' : 'Customer'; ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Status:</label>
                            <p><span class="status-badge status-<?php echo $view_user['status']; ?>"><?php echo ucfirst($view_user['status']); ?></span></p>
                        </div>
                        <div class="detail-group">
                            <label>Registered:</label>
                            <p><?php echo date('F d, Y g:i A', strtotime($view_user['created_at'])); ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Last Login:</label>
                            <p><?php echo $view_user['last_login'] ? date('F d, Y g:i A', strtotime($view_user['last_login'])) : 'Never'; ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Total Orders:</label>
                            <p><?php echo $view_user['order_count']; ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Total Spent:</label>
                            <p>$<?php echo number_format($view_user['total_spent'], 2); ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="manage_users.php" class="btn-secondary">Close</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            
            <?php if($edit_user): ?>
            <div class="modal-overlay" id="editModal">
                <div class="modal-container">
                    <div class="modal-header">
                        <h2>Edit User</h2>
                        <a href="manage_users.php" class="modal-close">&times;</a>
                    </div>
                    <form method="POST" action="manage_users.php" class="admin-form">
                        <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($edit_user['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($edit_user['phone']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" rows="3"><?php echo htmlspecialchars($edit_user['address']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="active" <?php echo $edit_user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $edit_user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="banned" <?php echo $edit_user['status'] == 'banned' ? 'selected' : ''; ?>>Banned</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="is_admin">
                                <option value="0" <?php echo !$edit_user['is_admin'] ? 'selected' : ''; ?>>Customer</option>
                                <option value="1" <?php echo $edit_user['is_admin'] ? 'selected' : ''; ?>>Administrator</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="edit_user" class="btn-primary">Save Changes</button>
                            <a href="manage_users.php" class="btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
            
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total Customers</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-shield"></i>
                    <h3><?php echo $totalAdmins; ?></h3>
                    <p>Administrators</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-check"></i>
                    <h3><?php echo $activeUsers; ?></h3>
                    <p>Active Users</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-clock"></i>
                    <h3><?php echo $inactiveUsers; ?></h3>
                    <p>Inactive Users</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-slash"></i>
                    <h3><?php echo $bannedUsers; ?></h3>
                    <p>Banned Users</p>
                </div>
            </div>
            
            
            <div class="users-search-bar">
                <form method="GET" action="manage_users.php" class="search-form">
                    <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Search</button>
                </form>
                <div class="filter-buttons">
                    <a href="manage_users.php" class="filter-btn <?php echo empty($status_filter) ? 'active' : ''; ?>">All</a>
                    <a href="manage_users.php?status=active" class="filter-btn <?php echo $status_filter == 'active' ? 'active' : ''; ?>">Active</a>
                    <a href="manage_users.php?status=inactive" class="filter-btn <?php echo $status_filter == 'inactive' ? 'active' : ''; ?>">Inactive</a>
                    <a href="manage_users.php?status=banned" class="filter-btn <?php echo $status_filter == 'banned' ? 'active' : ''; ?>">Banned</a>
                </div>
            </div>
            
            
            <div class="users-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($users)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No users found</span>
                            </tr>
                        <?php else: ?>
                            <?php foreach($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></span>
                                    <td>
                                        <?php echo htmlspecialchars($user['name']); ?>
                                        <?php if($user['id'] == $_SESSION['user_id']): ?>
                                            <span class="you-badge">(You)</span>
                                        <?php endif; ?>
                                    </span>
                                    <td><?php echo htmlspecialchars($user['email']); ?></span>
                                    <td><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : '-'; ?></span>
                                    <td>
                                        <?php if($user['is_admin']): ?>
                                            <span class="role-badge role-admin">Admin</span>
                                        <?php else: ?>
                                            <span class="role-badge role-user">Customer</span>
                                        <?php endif; ?>
                                    </span>
                                    <td>
                                        <span class="status-badge status-<?php echo $user['status']; ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </span>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                                    <td class="action-buttons">
                                        <a href="manage_users.php?view_id=<?php echo $user['id']; ?>" class="btn-view">View</a>
                                        <a href="manage_users.php?edit_id=<?php echo $user['id']; ?>" class="btn-edit">Edit</a>
                                        
                                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                                            <?php if($user['status'] == 'inactive'): ?>
                                                <!-- Restore button for inactive users -->
                                                <a href="manage_users.php?restore_id=<?php echo $user['id']; ?>" class="btn-restore" onclick="return confirm('Restore this user account?')">Restore</a>
                                            <?php endif; ?>
                                            
                                            <?php if($user['status'] == 'active'): ?>
                                                <!-- Deactivate button for active users -->
                                                <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" class="btn-deactivate" onclick="return confirm('Deactivate this user? They can be restored later.')">Deactivate</a>
                                            <?php endif; ?>
                                            
                                            <?php if($user['status'] == 'banned'): ?>
                                                <!-- Unban button for banned users -->
                                                <a href="manage_users.php?restore_id=<?php echo $user['id']; ?>" class="btn-restore" onclick="return confirm('Unban this user?')">Unban</a>
                                            <?php endif; ?>
                                            
                                            <!-- Permanent delete (only for users with no orders) -->
                                            <?php
                                            $checkOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                                            $checkOrders->execute([$user['id']]);
                                            $hasOrders = $checkOrders->fetchColumn() > 0;
                                            if(!$hasOrders && $user['status'] != 'banned'):
                                            ?>
                                                <a href="manage_users.php?permanent_delete_id=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('PERMANENTLY DELETE this user? This cannot be undone.')">Delete</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </span>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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