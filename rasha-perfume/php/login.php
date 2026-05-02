<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php?error=invalid_request');
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: ../login.php?error=empty_fields');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    
    if ($user['status'] == 'inactive') {
        header('Location: ../login.php?error=account_inactive');
        exit;
    }
    
    if ($user['status'] == 'banned') {
        header('Location: ../login.php?error=account_banned');
        exit;
    }
    
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_address'] = $user['address'];
    $_SESSION['is_admin'] = (bool)$user['is_admin'];
    
    
    $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$user['id']]);
    
    if ($user['is_admin']) {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../index.php?success=logged_in');
    }
} else {
    header('Location: ../login.php?error=invalid_credentials');
}
?>