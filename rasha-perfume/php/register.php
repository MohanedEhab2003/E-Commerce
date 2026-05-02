<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php?error=invalid_request');
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    header('Location: ../register.php?error=empty_fields');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../register.php?error=invalid_email');
    exit;
}

if (strlen($password) < 6) {
    header('Location: ../register.php?error=password_short');
    exit;
}

try {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword]);
    
    header('Location: ../login.php?success=registration_successful');
} catch(PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        header('Location: ../register.php?error=email_exists');
    } else {
        header('Location: ../register.php?error=registration_failed');
    }
}
?>