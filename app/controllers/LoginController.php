<?php
class LoginController {
    private $userModel;

    public function __construct() {
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User();
    }

    public function showLogin() {
        // Afficher la vue de connexion
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        include __DIR__ . '/../views/auth/login.php';
    }

    public function processLogin() {
        try {
            // Vérification CSRF
            if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid CSRF token', 403);
            }

            // Validation des inputs
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                throw new Exception('Tous les champs sont obligatoires');
            }

            // Authentification
            $user = $this->userModel->authenticate($username, $password);

            if (!$user) {
                throw new Exception('Identifiants invalides');
            }

            // Initialisation de session sécurisée
            $this->startSecureSession($user);

            // Redirection
            $this->redirectByRole($user->getRole());

        } catch (Exception $e) {
            $_SESSION['login_error'] = $e->getMessage();
            header('Location: /login');
            exit();
        }
    }

    private function verifyCsrfToken($token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    private function startSecureSession(User $user) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRole(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];
    }

    private function redirectByRole(string $role) {
        $routes = [
            'admin' => '/admin/dashboard',
            'vendeur' => '/vendeur/ventes',
            'fournisseur' => '/fournisseur/stocks'
        ];

        header('Location: ' . ($routes[$role] ?? '/'));
        exit();
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header('Location: /login');
        exit();
    }
}
