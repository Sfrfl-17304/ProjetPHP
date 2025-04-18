<?php
session_start();
require __DIR__ . '/../app/bootstrap.php';

// Génération CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$controller = new LoginController();

switch ($_GET['action'] ?? '') {
    case 'process':
        $controller->processLogin();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->showLogin();
}
