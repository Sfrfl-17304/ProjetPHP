<?php
require_once '../app/config/Database.php';// adjust path if needed
require_once '../app/models/User.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form inputs safely
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validate inputs
    if (empty($username) || empty($password) || empty($role)) {
        die("Veuillez remplir tous les champs.");
    }

    try {
        // Call the existing createUser method
        $result = User::createUser($username, $password, $role);

        if ($result) {
            header("Location: ../app/views/userGestion.php?success= Utilisateur Crée avec succès");
        } else {
            header("Location: ../app/views/userGestion.php?error=" . urlencode("Erreur lors de l'ajout de l'utilisateur."));
        }
        exit();
    } catch (Exception $e) {
        header("Location: ../app/views/userGestion.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    echo "Requête invalide.";
}


