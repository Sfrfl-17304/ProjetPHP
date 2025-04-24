<?php
require_once '../app/config/database.php';

$pdo = Database::getInstance();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $categoryId = $_POST['category_id'] ?? null;
    $supplierId = $_POST['supplier_id'] ?? null;

    // Check required fields
    if (!$categoryId || !$supplierId) {
        die("Invalid category or supplier");
    }

    // Handle file upload
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $destination = __DIR__ . '/uploads/' . $imageName;
        move_uploaded_file($imageTmpPath, $destination);
    }

    // Insert into database
    try {
        $redirect = $_SERVER['HTTP_REFERER'] ?? '../app/views/adminDashboard.php';
        $separator = strpos($redirect, '?') !== false ? '&' : '?';
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, quantity, price, image, category_id, supplier_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $description,
            $quantity,
            $price,
            $imageName,
            $categoryId,
            $supplierId
        ]);

        header("Location: {$redirect}{$separator}success=" . urlencode("Produit enregistrée avec succès."));        exit;

    } catch (PDOException $e) {
        die("Erreur lors de l'insertion : " . $e->getMessage());
    }
} else {
    die("Méthode non autorisée.");
}
