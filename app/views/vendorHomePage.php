<?php
session_start();
require_once __DIR__ . '/../bootstrap.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'vendeur') {
    header("Location: ../../public/login.php");
    exit();
}

$pdo = Database::getInstance();
$vendorId = $_SESSION['user']['id'];
// Récupérer les ventes avec noms
$stmt = $pdo->prepare("
    SELECT s.id, s.quantity, s.sale_date, s.client_info,
           p.name AS product_name,
           u.username AS seller_name
    FROM sales s
    LEFT JOIN products p ON s.product_id = p.id
    LEFT JOIN users u ON s.user_id = u.id
    WHERE s.user_id = ?
    ORDER BY s.sale_date DESC
");
$stmt->execute([$vendorId]);
$ventes = $stmt->fetchAll();



// Récupérer produits et vendeurs pour le formulaire
$produits = $pdo->query("SELECT id, name FROM products")->fetchAll();
$vendeurs = $pdo->query("SELECT id, username FROM users WHERE role = 'vendeur'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Stokly - Vendeur</title>
    <link rel="stylesheet" href="../../public/css/vendor.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>

<header class="header">
    <img src="../../public/assets/logo.jpg" alt="Stokly Logo" class="logo">
    <h1>Dashboard Vendeur</h1>
    <p class="slogan">Where stock meets smart.</p>
</header>

<main class="dashboard">

    <section class="card">
        <h2>Enregistrer une Vente</h2>
        <form method="POST" action="../../public/CreateSale.php">
            <div class="mb-3">
                <label for="product_id">Produit :</label>
                <select id="product_id" name="product_id" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($produits as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="user_id" value="<?= $vendorId ?>">

            <div class="mb-3">
                <label for="quantity">Quantité :</label>
                <input type="number" id="quantity" name="quantity" class="form-control" required min="1">
            </div>

            <div class="mb-3">
                <label for="client_info">Client :</label>
                <input type="text" id="client_info" name="client_info" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="sale_date">Date :</label>
                <input type="date" id="sale_date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <button type="submit" class="btn btn-success">Enregistrer</button>
        </form>
    </section>

    <section class="card">
        <h2>Ventes enregistrées</h2>
        <a href="dashboardTri.php" class="btn btn-danger" style="width: 20%">
            <i class="bi bi-box-seam me-1"></i> Consulter les stocks Triés
        </a>
        <table class="table table-bordered">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Vendeur</th>
                <th>Client</th>
                <th>Date</th>
                <th>Quantité</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ventes as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['seller_name']) ?></td>
                    <td><?= htmlspecialchars($row['client_info']) ?></td>
                    <td><?= htmlspecialchars($row['sale_date']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td class="text-center">
                        <a href="../../public/DeleteSale.php?id=<?= $row['id'] ?>" onclick="return confirm('Supprimer cette vente ?')">
                            <i class="bi bi-trash text-danger"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
