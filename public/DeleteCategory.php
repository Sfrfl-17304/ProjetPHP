<?php
require_once '../app/bootstrap.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../app/views/adminDashboard.php?error=ID invalide");
    exit;
}

$categoryId = (int)$_GET['id'];

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);

    header("Location: ../app/views/adminDashboard.php?success=Catégorie supprimée");
    exit;
} catch (PDOException $e) {
    header("Location: ../app/views/adminDashboard.php?error=" . urlencode($e->getMessage()));
    exit;
}
