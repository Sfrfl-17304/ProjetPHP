<?php

session_start();

require_once __DIR__ . '/../app/bootstrap.php'; // Initializes DB connection
require_once __DIR__ . '/../app/models/User.php';   // Loads the User class

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['username'];
    $password = md5($_POST['password']); // md5, as you requested

    $user = User::authenticate($login, $password);

    if ($user) {
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRole()
        ];

        switch ($user->getRole()) {
            case 'admin':
                header("Location: ../app/views/AdminDashboard.php");
                break;
            case 'vendeur':
                header("Location: ../app/views/vendorHomePage.php");
                break;
            case 'fournisseur':
                header("Location: ../app/views/supplierHomePage.php");
                break;
            default:
                header("Location: ../app/views/error.php");
                break;
        }
        exit();
    } else {
        $_SESSION['login_error'] = "<i>Nom d'utilisateur ou mot de passe incorrect.</i>";
        header("Location: ../public/login.php");
        exit();
    }
} else {
    // If someone tries to access Auth.php directly without submitting form
    header("Location: ../public/login.php");
    exit();
}
