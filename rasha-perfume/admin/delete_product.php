<?php
require_once '../php/config.php';

if (!isAdmin()) {
    header('Location: ../index.php?error=unauthorized');
    exit;
}

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?section=products&success=product_deleted');
    } catch(PDOException $e) {
        header('Location: index.php?section=products&error=delete_failed');
    }
} else {
    header('Location: index.php?section=products&error=invalid_id');
}
exit;
?>