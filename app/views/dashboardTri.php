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
            SELECT p.id, p.name, p.description, p.price, p.quantity, p.image,
                   u.username AS fournisseur_name, c.name AS category_name,
                   COALESCE(SUM(s.quantity), 0) AS total_vendu
            FROM products p
            LEFT JOIN users u ON p.supplier_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN sales s ON p.id = s.product_id
            GROUP BY p.id
            ORDER BY total_vendu DESC";
        break;

    case 'fournisseur':
        $sql = "
            SELECT p.id, p.name, p.description, p.price, p.quantity, p.image,
                   u.username AS fournisseur_name, c.name AS category_name
            FROM products p
            LEFT JOIN users u ON p.supplier_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY u.username ASC";
        break;

    default: // stock
        $sql = "
            SELECT p.id, p.name, p.description, p.price, p.quantity, p.image,
                   u.username AS fournisseur_name, c.name AS category_name
            FROM products p
            LEFT JOIN users u ON p.supplier_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.quantity DESC";
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
            font-family: 'Segoe UI', sans-serif;
            background-color: #1e1e1e; /* original dark background */
            color: #fff;
            padding: 30px;
        }

        h1 {
            color: #ff4d4d;
            margin-bottom: 20px;
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

        .card {
            background-color: #2b2b2b; /* slightly lighter dark */
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            padding: 25px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            background-color: #1c1c1c; /* dark table bg */
            color: #fff;
        }

        thead {
            background-color: #333;
            color: #ffb3b3;
        }
        th{
            background-color: #444; /* lighter header background */
            color: #fff; /* bright white text */
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #444;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background-color: #242424;
        }

        tr:last-child td {
            border-bottom: none;
        }

        img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 6px;
            object-fit: cover;
        }
    </style>


</head>
<body>

<h1>Stokly - Tri des Produits</h1>

<div class="filters">
    <a href="?tri=stock" class="btn btn-outline-danger <?= $tri == 'stock' ? 'active' : '' ?>">
        <i class="bi bi-box-seam me-1"></i> Stock
    </a>
    <a href="?tri=popularite" class="btn btn-outline-danger <?= $tri == 'popularite' ? 'active' : '' ?>">
        <i class="bi bi-graph-up-arrow me-1"></i> Popularité
    </a>
    <a href="?tri=fournisseur" class="btn btn-outline-danger <?= $tri == 'fournisseur' ? 'active' : '' ?>">
        <i class="bi bi-person-badge me-1"></i> Fournisseur
    </a>
</div>

<div class="card">
    <table>
    <thead class="table-light">
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Catégorie</th>
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
            <td>
                <?php if (!empty($p['image'])): ?>
                    <img src="../../public/uploads/<?= htmlspecialchars($p['image']) ?>" alt="Produit">
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td><?= htmlspecialchars($p['price']) ?> MAD</td>
            <td><?= htmlspecialchars($p['quantity']) ?></td>
            <td><?= htmlspecialchars($p['category_name']) ?></td>
            <td><?= htmlspecialchars($p['fournisseur_name']) ?></td>
            <?php if ($tri === 'popularite'): ?>
                <td><?= htmlspecialchars($p['total_vendu']) ?></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

</body>
</html>
