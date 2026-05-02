<?php
require_once 'php/config.php';


$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';


$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


$catStmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rasha Perfume - Luxury Fragrances</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php"><i class="fas fa-spray-can"></i> Rasha Perfume</a>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="index.php?page=products" class="nav-link">Products</a>
                <a href="cart.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> Cart 
                    <span class="cart-count">
                        <?php 
                        if(isset($_SESSION['cart'])) {
                            $count = array_sum($_SESSION['cart']);
                            echo $count;
                        } else {
                            echo '0';
                        }
                        ?>
                    </span>
                </a>
                <?php if(isLoggedIn()): ?>
                    <div class="user-menu">
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <?php if(isAdmin()): ?>
                            <a href="admin/index.php" class="btn-auth">Admin Panel</a>
                        <?php endif; ?>
                        <a href="php/logout.php" class="btn-auth">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn-auth">Login</a>
                    <a href="register.php" class="btn-auth">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Hero Section -->
            <section class="hero">
                <div class="hero-content">
                    <h1>Discover Your Signature Scent at Rasha</h1>
                    <p>Explore our collection of luxurious perfumes from world-renowned brands</p>
                    <a href="#products" class="btn-primary">Shop Now</a>
                </div>
            </section>

            <!-- Categories -->
            <section class="categories">
                <h2>Shop by Category</h2>
                <div class="category-grid">
                    <?php foreach($categories as $cat): ?>
                        <a href="index.php?category=<?php echo urlencode($cat); ?>" class="category-card">
                            <i class="fas <?php 
                                echo $cat == 'Women' ? 'fa-female' : ($cat == 'Men' ? 'fa-male' : 'fa-genderless'); 
                            ?>"></i>
                            <h3><?php echo htmlspecialchars($cat); ?></h3>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            
            <section class="filters">
                <div class="search-bar">
                    <form method="GET" action="index.php">
                        <input type="text" name="search" placeholder="Search perfumes..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </section>

            
            <section id="products" class="products-section">
                <h2>Our Fragrances</h2>
                <?php if(empty($products)): ?>
                    <p class="no-products">No products found.</p>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach($products as $product): ?>
                            <div class="product-card">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='https://via.placeholder.com/300'">
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...</p>
                                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                                    <p class="stock">Stock: <?php echo $product['stock']; ?> left</p>
                                    <?php if(isLoggedIn() && $product['stock'] > 0): ?>
                                        <form method="POST" action="php/add_to_cart.php">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                                        </form>
                                    <?php elseif(!isLoggedIn()): ?>
                                        <a href="login.php" class="btn-add-to-cart">Login to Buy</a>
                                    <?php else: ?>
                                        <button class="btn-add-to-cart" disabled>Out of Stock</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Rasha Perfume. All rights reserved.</p>
            <p>Luxury fragrances for every occasion</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>