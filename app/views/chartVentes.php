<?php
require_once __DIR__ . '/../bootstrap.php';
session_start();

// Vérification d'accès
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pdo = Database::getInstance();

$fromDate = $_GET['from'] ?? null;
$toDate = $_GET['to'] ?? null;

$dates = [];
$quantites = [];

if ($fromDate && $toDate) {
    $stmt = $pdo->prepare("
        SELECT sale_date, SUM(quantity) AS total_vendu
        FROM sales
        WHERE sale_date BETWEEN :from AND :to
        GROUP BY sale_date
        ORDER BY sale_date
    ");
    $stmt->execute([
        'from' => $fromDate,
        'to' => $toDate
    ]);

    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultats as $row) {
        $dates[] = $row['sale_date'];
        $quantites[] = $row['total_vendu'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Graphique des Ventes</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .slogan {
            color: #ffcccb;
            font-size: 14px;
            margin-top: 5px;
        }
        .container-custom {
            max-width: 1000px;
            margin: auto;
            padding: 30px;
        }
        .card {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #b71c1c;
            text-align: center;
            margin-bottom: 30px;
        }
        canvas {
            width: 100% !important;
            height: auto !important;
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
    <h1> Évolution des Ventes</h1>
    <p class="slogan">Visualisation graphique des quantités vendues</p>
</header>

<div class="container-custom">
    <div class="card">
        <?php if ($fromDate && $toDate): ?>
            <p><strong>Période :</strong> du <span class="text-danger"><?= htmlspecialchars($fromDate) ?></span> au <span class="text-danger"><?= htmlspecialchars($toDate) ?></span></p>

            <?php if (!empty($dates)): ?>
                <canvas id="salesChart"></canvas>
            <?php else: ?>
                <div class="alert alert-warning">Aucune vente trouvée sur cette période.</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">Aucune période sélectionnée. Veuillez passer par le Dashboard.</div>
        <?php endif; ?>

        <a href="adminDashboard.php" class="btn btn-back mt-4">⬅ Retour au Dashboard</a>
    </div>
</div>

<?php if (!empty($dates)): ?>
    <script>
        const ctx = document.getElementById('salesChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{
                    label: 'Quantité vendue',
                    data: <?= json_encode($quantites) ?>,
                    borderColor: '#d32f2f',
                    backgroundColor: '#f8d7da',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#222'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#444' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#444', stepSize: 1 }
                    }
                }
            }
        });
    </script>
<?php endif; ?>

</body>
</html>
