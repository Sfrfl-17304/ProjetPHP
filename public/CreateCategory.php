<?php
require_once '../app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $name = trim($_POST['name']);

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);

        header("Location: ../app/views/adminDashboard.php?success=Catégorie ajoutée");
        exit;
    } catch (PDOException $e) {
        header("Location: ../app/views/adminDashboard.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: ../app/views/adminDashboard.php?error=Nom de catégorie requis");
    exit;
}
