<?php
require_once '../app/bootstrap.php'; // charge db et classes

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../app/views/adminDashboard.php?error=ID de vente invalide");
    exit;
}

$saleId = (int) $_GET['id'];

try {
    $pdo = Database::getInstance();

    // Récupérer les infos de la vente pour remettre la quantité dans le stock
    $stmt = $pdo->prepare("SELECT product_id, quantity FROM sales WHERE id = ?");
    $stmt->execute([$saleId]);
    $vente = $stmt->fetch();

    if (!$vente) {
        header("Location: ../app/views/adminDashboard.php?error=Vente introuvable");
        exit;
    }

    $product_id = $vente['product_id'];
    $quantity = $vente['quantity'];

    // Supprimer la vente
    $stmt = $pdo->prepare("DELETE FROM sales WHERE id = ?");
    $stmt->execute([$saleId]);

    // Remettre la quantité dans le stock du produit
    $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
    $stmt->execute([$quantity, $product_id]);

    $redirect = $_SERVER['HTTP_REFERER'] ?? '../app/views/adminDashboard.php';
    header("Location: $redirect?success=Vente Supprimée et stock restoré");
    exit;
} catch (PDOException $e) {
    $redirect = $_SERVER['HTTP_REFERER'] ?? '../views/adminDashboard.php';
    header("Location: $redirect?error=" . urlencode("Erreur ..."));
    exit;

}
