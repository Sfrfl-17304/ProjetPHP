<?php
// Connexion à la base
$host = 'sql7.freesqldatabase.com';
$dbname = 'sql7774461';
$username = 'sql7774461';
$password = 'rqxhprUNxK';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Choix du tri
$tri = $_GET['tri'] ?? 'stock';

switch ($tri) {
    case 'popularite':
        $sql = "
            SELECT p.id, p.name, p.description, p.price, p.quantity, p.supplier_id, 
                   COALESCE(SUM(s.quantity), 0) as total_vendu
            FROM products p
            LEFT JOIN sales s ON s.product_id = p.id
            GROUP BY p.id
            ORDER BY total_vendu DESC";
        break;

    case 'fournisseur':
        $sql = "SELECT * FROM products ORDER BY supplier_id ASC";
        break;

    default: // stock
        $sql = "SELECT * FROM products ORDER BY quantity DESC";
        break;
}

$produits = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stokly - Tri Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e1e;
            color: #fff;
            padding: 20px;
        }
        h1 {
            color: #ff4d4d;
        }
        .filters {
            margin-bottom: 20px;
        }
        .filters .btn {
            margin-right: 10px;
            font-weight: bold;
        }
        .filters .active {
            background-color: #ff4d4d !important;
            color: white !important;
            border-color: #ff4d4d !important;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #444;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #333;
        }
        tr:nth-child(even) {
            background-color: #2c2c2c;
        }
    </style>
</head>
<body>

<h1>Stokly – Tri Produits</h1>

<div class="filters">
    <a href="?tri=stock" class="btn btn-outline-light <?= $tri == 'stock' ? 'active' : '' ?>">
        <i class="bi bi-box-seam me-1"></i> Stock
    </a>
    <a href="?tri=popularite" class="btn btn-outline-light <?= $tri == 'popularite' ? 'active' : '' ?>">
        <i class="bi bi-graph-up-arrow me-1"></i> Popularité
    </a>
    <a href="?tri=fournisseur" class="btn btn-outline-light <?= $tri == 'fournisseur' ? 'active' : '' ?>">
        <i class="bi bi-person-badge me-1"></i> Fournisseur
    </a>
</div>

<table class="table table-dark table-bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Fournisseur</th>
        <?php if ($tri === 'popularite'): ?>
            <th>Total Vendu</th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($produits as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['id']) ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td><?= htmlspecialchars($p['price']) ?> MAD</td>
            <td><?= htmlspecialchars($p['quantity']) ?></td>
            <td><?= htmlspecialchars($p['supplier_id']) ?></td>
            <?php if ($tri === 'popularite'): ?>
                <td><?= htmlspecialchars($p['total_vendu']) ?></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
