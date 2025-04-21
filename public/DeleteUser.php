<?php
require_once __DIR__ . '/../app/bootstrap.php';


// Simuler un utilisateur connecté pour cet exemple
// Remplace ceci par la session réelle de l'utilisateur connecté

// Vérifie que l'ID est présent et est un nombre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../app/views/AdminDashboard.php?error=" . urlencode("ID utilisateur invalide."));
    exit();
}

$id = (int)$_GET['id'];

try {
    $success = User::deleteUserById($id);
    if ($success) {
        header("Location: ../app/views/AdminDashboard.php?success=" . urlencode("Utilisateur supprimé."));
    } else {
        header("Location: ../app/views/AdminDashboard.php?error=" . urlencode("Échec de la suppression."));
    }
} catch (Exception $e) {
    header("Location: ../app/views/AdminDashboard.php?error=" . urlencode($e->getMessage()));
}
exit();
