<?php
require_once __DIR__ . '/../models/Vente.php';

class VenteManager {
    private $pdo;

    public function __construct() {
        $host = 'sql7.freesqldatabase.com';
        $dbname = 'sql7774461';
        $username = 'sql7774461';
        $password = 'rqxhprUNxK';
        $port = 3306;

        try {
            $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function ajouterVente(Vente $vente) {
        $sql = "INSERT INTO sales (product_id, user_id, quantity, sale_date, client_info) 
                VALUES (:product_id, :user_id, :quantity, :sale_date, :client_info)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'product_id' => $vente->getProductId(),
            'user_id' => $vente->getUserId(),
            'quantity' => $vente->getQuantity(),
            'sale_date' => $vente->getSaleDate(),
            'client_info' => $vente->getClientInfo()
        ]);
    }

    public function supprimerVente($id) {
        $stmt = $this->pdo->prepare("DELETE FROM sales WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getVentes() {
        $stmt = $this->pdo->query("SELECT * FROM sales ORDER BY sale_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}