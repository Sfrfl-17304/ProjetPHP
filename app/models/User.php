<?php
class User {
    private $id;
    private $username;
    private $password;
    private $role;

    // Connexion à la base de données
    private static $db;

    public function __construct($username, $password, $role) {
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }


    public static function setDb(PDO $db) {
        self::$db = $db;
    }
    public static function init()
    {
        if (!self::$db) {
            self::$db = Database::getInstance(); // Assuming this returns a PDO object
        }
    }

    public static function createUser(string $username, string $password, string $role): bool {
        self::init();
        $db = self::$db;

        try {
            $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $hashedPassword = MD5($password);
            return $stmt->execute([$username, $hashedPassword, $role]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        }
    }


    public static function deleteUserById(int $id): bool {


        $db = self::$db;

        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }


    // Méthodes d'authentification
    public static function authenticate($username, $password) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($user = $stmt->fetch()) {
            // Compare directly with md5 hash
            if ($password === $user['password']) {
                return self::createFromArray($user);
            }
        }
        return null;
    }


    // Factory method
    private static function createFromArray(array $data): User {
        $user = new self($data['username'], $data['password'], $data['role']);
        $user->id = $data['id'];
        return $user;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getRole(): string { return $this->role; }


    // Vérification des rôles
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isVendeur(): bool {
        return $this->role === 'vendeur';
    }

    public function isFournisseur(): bool {
        return $this->role === 'fournisseur';
    }

    // Permissions métier
    public function canManageProducts(): bool {
        return $this->isAdmin() || $this->isFournisseur();
    }

    public function canViewSales(): bool {
        return $this->isAdmin() || $this->isVendeur();
    }
}