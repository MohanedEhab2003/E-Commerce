<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rasha Perfume</title>
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
                    <h2>Login to Your Account</h2>
                    
                    <?php if(isset($_GET['error'])): ?>
                        <?php if($_GET['error'] == 'account_inactive'): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Account Deactivated</strong>
                                <p>Your account has been deactivated. Please contact us to reactivate your account.</p>
                                <div class="contact-support">
                                    <i class="fas fa-envelope"></i> Email us at: 
                                    <a href="mailto:rasha@gmail.com">rasha@gmail.com</a>
                                    <br>
                                    <small>Please include your email address and reason for reactivation request.</small>
                                </div>
                            </div>
                        <?php elseif($_GET['error'] == 'account_banned'): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-ban"></i>
                                <strong>Account Banned</strong>
                                <p>Your account has been banned due to violation of our terms of service.</p>
                                <div class="contact-support">
                                    <i class="fas fa-envelope"></i> Contact us at: 
                                    <a href="mailto:rasha@gmail.com">rasha@gmail.com</a>
                                    <br>
                                    <small>If you believe this is a mistake, please contact our support team.</small>
                                </div>
                            </div>
                        <?php elseif($_GET['error'] == 'invalid_credentials'): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-times-circle"></i>
                                Invalid email or password. Please try again.
                            </div>
                        <?php elseif($_GET['error'] == 'empty_fields'): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i>
                                Please enter both email and password.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['success'])): ?>
                        <?php if($_GET['success'] == 'registration_successful'): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                Registration successful! Please login with your credentials.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="php/login.php" class="auth-form">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" required placeholder="your@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" required placeholder="Enter your password">
                        </div>
                        
                        <button type="submit" class="btn-primary btn-block">Login</button>
                    </form>
                    
                    <p class="auth-link">Don't have an account? <a href="register.php">Sign Up</a></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>