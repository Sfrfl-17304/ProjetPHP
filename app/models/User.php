<?php
class User {
    private $id;
    private $username;
    private $password;
    private $role;

    // Connexion à la base de données
    private static $db;

    public static function setDb(PDO $db) {
        self::$db = $db;
    }

    public static function createUser(array $data, User $requester): User {
        if (!$requester->isAdmin()) {
            throw new Exception("Permission denied");
        }

        self::validateUserData($data);

        $db = self::$db;
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO users (username, password, role) 
                VALUES (?, ?, ?)
            ");

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt->execute([
                $data['username'],
                $hashedPassword,
                $data['role']
            ]);

            $newUserId = $db->lastInsertId();
            $db->commit();

            return self::findById($newUserId);
        } catch(PDOException $e) {
            $db->rollBack();
            throw new Exception("Erreur de création : " . $e->getMessage());
        }
    }

    // Méthodes d'authentification
    public static function authenticate(string $username, string $password): ?User {
        try {
            $stmt = self::$db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if(password_verify($password, $row['password'])) {
                    return self::createFromArray($row);
                }
            }
            return null;
        } catch(PDOException $e) {
            throw new Exception("Erreur d'authentification : " . $e->getMessage());
        }
    }

    // Factory method
    private static function createFromArray(array $data): User {
        $user = new self();
        $user->id = $data['id'];
        $user->username = $data['username'];
        $user->password = $data['password'];
        $user->role = $data['role'];
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