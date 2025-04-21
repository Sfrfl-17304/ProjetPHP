<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Product.php';

// Initialiser la connexion
$db = Database::getInstance();

// Injecter la connexion dans les classes
User::setDb($db);
Product::setDb($db);