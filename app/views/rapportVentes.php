<?php
require_once __DIR__ .'/../bootstrap.php';
session_start();
$client = $_GET['client'] ?? null;

if ($client) {
    // Redirige vers le script PDF si formulaire soumis
    header("Location: ../../public/exportClientPDF.php?client=" . urlencode($client));
    exit;
}



$from = $_GET['from'] ?? ($_POST['from'] ?? null);
$to = $_GET['to'] ?? ($_POST['to'] ?? null);


// Vérification d'accès admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pdo = Database::getInstance();

$fromDate = $_GET['from'] ?? null;
$toDate = $_GET['to'] ?? null;
$ventes = [];
$totalCA = 0;

if ($fromDate && $toDate) {
    $stmt = $pdo->prepare("
        SELECT s.id, s.quantity, s.sale_date, s.client_info,
               p.name AS product_name, p.price,
               u.username AS vendeur
        FROM sales s
        JOIN products p ON s.product_id = p.id
        JOIN users u ON s.user_id = u.id
        WHERE s.sale_date BETWEEN ? AND ?
        ORDER BY s.sale_date DESC
    ");
    $stmt->execute([$fromDate, $toDate]);
    $ventes = $stmt->fetchAll();

    foreach ($ventes as $vente) {
        $totalCA += $vente['quantity'] * $vente['price'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Ventes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }
        .header {
            background-color: #d32f2f;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container-custom {
            padding: 30px;
            max-width: 1200px;
            margin: auto;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #b71c1c;
        }
        .table thead {
            background-color: #f8d7da;
        }
        .total-ca {
            font-size: 1.2rem;
            font-weight: bold;
            color: #b71c1c;
        }
        .btn-back {
            background-color: #d32f2f;
            color: white;
        }
        .btn-back:hover {
            background-color: #b71c1c;
        }
        .logo {
            height: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header class="header">
    <img class="logo" src="../../public/assets/logo.jpg">
    <h1> Rapport des Ventes</h1>
    <p class="slogan">Résumé des ventes sur une période donnée</p>
</header>

<div class="container-custom">
    <div class="card">
        <form method="GET" class="mb-3">
            <label for="client" class="form-label">Choisir un client :</label>
            <input type="text" name="client" id="client" class="form-control" placeholder="Nom du client" required>
            <button type="submit" class="btn btn-danger mt-2">Générer le PDF</button>
        </form>

        <?php if ($fromDate && $toDate): ?>
            <p>
                <strong>Période :</strong>
                du <span class="text-danger"><?= htmlspecialchars($fromDate) ?></span>
                au <span class="text-danger"><?= htmlspecialchars($toDate) ?></span>
            </p>

            <?php if (count($ventes) > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Produit</th>
                        <th>Vendeur</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                        <th>Chiffre d'affaires</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ventes as $v): ?>
                        <tr>
                            <td><?= $v['id'] ?></td>
                            <td><?= htmlspecialchars($v['product_name']) ?></td>
                            <td><?= htmlspecialchars($v['vendeur']) ?></td>
                            <td><?= htmlspecialchars($v['client_info']) ?></td>
                            <td><?= htmlspecialchars($v['sale_date']) ?></td>
                            <td><?= htmlspecialchars($v['quantity']) ?></td>
                            <td><?= number_format($v['price'], 2) ?> MAD</td>
                            <td><?= number_format($v['price'] * $v['quantity'], 2) ?> MAD</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="total-ca"> Total du chiffre d'affaires : <?= number_format($totalCA, 2) ?> MAD</p>
            <?php else: ?>
                <div class="alert alert-warning">Aucune vente trouvée pour cette période.</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                Veuillez d'abord sélectionner une période depuis le <a href="adminDashboard.php">Dashboard</a>.
            </div>
        <?php endif; ?>

        <?php if ($from && $to): ?>
            <a href="../../public/exportRapportPDF.php?from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>"
               class="btn btn-danger" target="_blank">
                📄 Exporter en PDF
            </a>
        <?php endif; ?>



        <a href="adminDashboard.php" class="btn btn-back mt-3">⬅ Retour au Dashboard</a>
    </div>
</div>

</body>
</html>
