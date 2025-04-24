<?php
// Connexion à la base de données
$host = 'sql7.freesqldatabase.com';
$dbname = 'sql7774461';
$username = 'sql7774461';
$password = 'rqxhprUNxK';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$produits = [];
$fournisseur_id = null;

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fournisseur') {
    header("Location: ../../public/login.php");
    exit();
}

$fournisseur_id = $_SESSION['user']['id'];

// Récupérer les produits de ce fournisseur
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.supplier_id = :id
");
$stmt->execute(['id' => $fournisseur_id]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stokly - Fournisseur</title>
    <link rel="stylesheet" href="../../public/css/supplier.css">
</head>
<body>

<header class="header">
    <img src="../../public/assets/logo.jpg" alt="Logo" class="logo">
    <h1>Dashboard Fournisseur</h1>
    <p class="slogan">Visualisez vos produits en stock</p>
</header>

<main class="dashboard">



    <section class="card">
        <h2>Vos produits en stock</h2>
        <?php if (count($produits) > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Catégorie</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($produits as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['id']) ?></td>
                        <td><?= htmlspecialchars($prod['name']) ?></td>
                        <td><?= htmlspecialchars($prod['description']) ?></td>
                        <td><?= htmlspecialchars($prod['price']) ?> MAD</td>
                        <td><?= htmlspecialchars($prod['quantity']) ?></td>
                        <td><?= htmlspecialchars($prod['category_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Vous n'avez aucun produit enregistré.</p>
        <?php endif; ?>
    </section>

</main>

</body>
</html>