<?php
session_start();
require_once '../app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $user_id = ($_SESSION['user']['role'] === 'vendeur') ? $_SESSION['user']['id'] : ($_POST['user_id'] ?? null);
    $quantity = (int) $_POST['quantity'];
    $sale_date = $_POST['sale_date'];
    $client_info = trim($_POST['client_info']);

    try {
        $pdo = Database::getInstance();

        // 1. Vérifier la quantité disponible
        $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $current_stock = $stmt->fetchColumn();

        if ($current_stock === false) {
            throw new Exception("Produit introuvable.");
        }

        // 2. Si quantité insuffisante
        if ($quantity > $current_stock) {
            $redirect = $_SERVER['HTTP_REFERER'] ?? '../app/views/adminDashboard.php';
            $separator = strpos($redirect, '?') !== false ? '&' : '?';
            header("Location: {$redirect}{$separator}error=" . urlencode("Stock insuffisant pour ce produit."));
            exit;
        }

        // 3. Enregistrer la vente
        $stmt = $pdo->prepare("
            INSERT INTO sales (product_id, user_id, client_info, quantity, sale_date)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$product_id, $user_id, $client_info, $quantity, $sale_date]);

        // 4. Mettre à jour le stock
        $new_stock = $current_stock - $quantity;
        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_stock, $product_id]);

        // 5. Vérifier stock faible
        $redirect = $_SERVER['HTTP_REFERER'] ?? '../app/views/adminDashboard.php';
        $separator = strpos($redirect, '?') !== false ? '&' : '?';

        if ($new_stock < 50) {
            header("Location: {$redirect}{$separator}success=" . urlencode("Vente enregistrée, mais stock faible : {$new_stock} unités restantes."));
        } else {
            header("Location: {$redirect}{$separator}success=" . urlencode("Vente enregistrée avec succès."));
        }

        exit;
    } catch (Exception $e) {
        $redirect = $_SERVER['HTTP_REFERER'] ?? '../app/views/adminDashboard.php';
        $separator = strpos($redirect, '?') !== false ? '&' : '?';
        header("Location: {$redirect}{$separator}error=" . urlencode($e->getMessage()));
        exit;
    }
}
