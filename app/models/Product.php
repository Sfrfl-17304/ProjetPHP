<?php
class Product {
    private $id;
    private $name;
    private $description;
    private $price;
    private $quantity;
    private $categoryId;
    private $supplierId;
    private $image;

    private static $db;

    public function __construct($name, $description, $price, $quantity, $categoryId, $supplierId, $image = null) {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->categoryId = $categoryId;
        $this->supplierId = $supplierId;
        $this->image = $image;
    }

    public static function setDb(PDO $db) {
        self::$db = $db;
    }

    private static function init() {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }

    // CRUD Operations
    public static function createProduct(
        string $name,
        string $description,
        float $price,
        int $quantity,
        int $categoryId,
        int $supplierId,
        string $image = null
    ): bool {
        self::init();

        try {
            $stmt = self::$db->prepare("INSERT INTO products 
                (name, description, price, quantity, category_id, supplier_id, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)");

            return $stmt->execute([
                $name,
                $description,
                $price,
                $quantity,
                $categoryId,
                $supplierId,
                $image
            ]);
        } catch (PDOException $e) {
            throw new Exception("Product creation failed: " . $e->getMessage());
        }
    }

    public static function getAllProducts(): array {
        self::init();

        try {
            $stmt = self::$db->query("SELECT * FROM products");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching products: " . $e->getMessage());
        }
    }

    public static function getProductById(int $id): ?array {
        self::init();

        try {
            $stmt = self::$db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new Exception("Error fetching product: " . $e->getMessage());
        }
    }

    public static function updateProduct(
        int $id,
        string $name,
        string $description,
        float $price,
        int $quantity,
        int $categoryId,
        int $supplierId,
        string $image = null
    ): bool {
        self::init();

        try {
            $stmt = self::$db->prepare("UPDATE products SET
                name = ?,
                description = ?,
                price = ?,
                quantity = ?,
                category_id = ?,
                supplier_id = ?,
                image = ?
                WHERE id = ?");

            return $stmt->execute([
                $name,
                $description,
                $price,
                $quantity,
                $categoryId,
                $supplierId,
                $image,
                $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Product update failed: " . $e->getMessage());
        }
    }
    public static function deleteProductById($id)
    {
        try {
            $stmt = self::$db->prepare("DELETE FROM products WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Vérifie si c’est une violation de contrainte étrangère
            if ($e->getCode() === '23000') {
                throw new Exception("Impossible de supprimer ce produit car il est lié à des ventes.");
            } else {
                throw new Exception("Product deletion failed: " . $e->getMessage());
            }
        }
    }


    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getPrice(): float { return $this->price; }
    public function getQuantity(): int { return $this->quantity; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getSupplierId(): int { return $this->supplierId; }
    public function getImage(): ?string { return $this->image; }
}