<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=sql7.freesqldatabase.com;dbname=sql7774461;charset=utf8mb4",
                "sql7774461",
                "rqxhprUNxK",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO {
        if(!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
