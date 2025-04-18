<?php
require_once __DIR__ . '/Database.php';

// Initialiser la connexion
$db = Database::getInstance();

// Injecter la connexion dans les classes
User::setDb($db);
// Product::setDb($db);
