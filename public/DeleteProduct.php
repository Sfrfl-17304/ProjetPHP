<?php
require_once '../app/bootstrap.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../app/views/adminDashboard.php?error=ID du produit invalide");
    exit;
}

$productId = (int)$_GET['id'];

try {
    Product::deleteProductById($productId);

    header("Location: ../app/views/adminDashboard.php?success=Produit supprimé avec succès");
    exit;
} catch (PDOException $e) {
    header("Location: ../app/views/adminDashboard.php?error=" . urlencode("Erreur : " . $e->getMessage()));
    exit;
}
