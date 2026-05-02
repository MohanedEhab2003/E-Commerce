<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Rasha Perfume</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php"><i class="fas fa-spray-can"></i> Rasha Perfume</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="auth-container">
                <div class="auth-box">
                    <h2>Create New Account</h2>
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="php/register.php" class="auth-form">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password (min 6 characters)</label>
                            <input type="password" name="password" required minlength="6">
                        </div>
                        
                        <button type="submit" class="btn-primary btn-block">Register</button>
                    </form>
                    
                    <p class="auth-link">Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>