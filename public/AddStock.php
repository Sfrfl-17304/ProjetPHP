<?php
session_start();
require_once '../app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $added_quantity = (int) ($_POST['quantity'] ?? 0);

    if (!$product_id || $added_quantity <= 0) {
        $redirect = strtok($_SERVER['HTTP_REFERER'], '?');
        header("Location: {$redirect}?error=" . urlencode("Entrée invalide."));
        exit;
    }

    try {
        $pdo = Database::getInstance();

        // 1. Vérifier que le produit existe
        $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $current_quantity = $stmt->fetchColumn();

        if ($current_quantity === false) {
            throw new Exception("Produit introuvable.");
        }

        // 2. Calculer nouvelle quantité
        $new_quantity = $current_quantity + $added_quantity;

        // 3. Mettre à jour la base de données
        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $product_id]);

        // 4. Rediriger avec message de succès
        $redirect = strtok($_SERVER['HTTP_REFERER'], '?');
        header("Location: {$redirect}?success=" . urlencode("Stock mis à jour. Nouveau stock : {$new_quantity}"));
        exit;

    } catch (Exception $e) {
        $redirect = strtok($_SERVER['HTTP_REFERER'], '?');
        header("Location: {$redirect}?error=" . urlencode("Erreur : " . $e->getMessage()));
        exit;
    }
} else {
    // Si accès direct
    header("Location: /login.php");
    exit;
}
