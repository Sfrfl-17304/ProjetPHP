<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once __DIR__ . '/../bootstrap.php';
$pdo = Database::getInstance();

$stmt = $pdo->query("SELECT s.id, s.client_info, s.sale_date, s.quantity, p.name AS product_name, u.username AS seller
                      FROM sales s
                      LEFT JOIN products p ON s.product_id = p.id
                      LEFT JOIN users u ON s.user_id = u.id
                      ORDER BY s.sale_date DESC");
$ventes = $stmt->fetchAll();

$produits = $pdo->query("SELECT id, name FROM products")->fetchAll();
$vendeurs = $pdo->query("SELECT id, username FROM users WHERE role = 'vendeur'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Ventes - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        h1 {
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #dc3545;
            color: white;
        }
        .btn-outline-success:hover {
            background-color: #198754;
            color: white;
        }
        .btn-danger, .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
    </style>
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

<nav class="navbar navbar-expand-lg navbar-dark bg-danger px-4">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../public/assets/logo.jpg" alt="Stokly Logo" style="height: 40px; margin-right: 10px;">
        <span class="fw-bold">Stokly Admin</span>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a href="adminDashboard.php" class="nav-link"><i class="bi bi-house me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a href="adminDashboardProduct.php" class="nav-link "><i class="bi bi-box-seam me-1"></i> Produits</a></li>
            <li class="nav-item"><a href="userGestion.php" class="nav-link"><i class="bi bi-people me-1"></i> Utilisateurs</a></li>
            <li class="nav-item"><a href="salesGestion.php" class="nav-link active"><i class="bi bi-currency-dollar me-1"></i> Ventes</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-danger mb-4">Gestion des Ventes</h1>

    <div class="d-flex flex-wrap gap-3 mb-4">
        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addSaleModal">
            <i class="bi bi-plus-circle"></i> Ajouter une vente
        </button>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rapportModal">
            <i class="bi bi-file-earmark-text"></i> Générer un rapport
        </button>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#chartModal">
            <i class="bi bi-graph-up"></i> Voir graphiques de ventes
        </button>
        <form method="GET" action="../../public/exportClientPDF.php" class="d-flex align-items-center">
            <input type="text" name="client" class="form-control me-2" placeholder="Nom du client" required>
            <button style="width: 80%" type="submit" class="btn btn-outline-danger">
                <i class="bi bi-printer"></i> Facture Client
            </button>
        </form>

    </div>


    <div class="table-container">
        <div class="table-responsive">
            <table class="table">
                <thead>
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
                <?php foreach ($ventes as $vente): ?>
                    <tr>
                        <td><?= $vente['id'] ?></td>
                        <td><?= htmlspecialchars($vente['product_name']) ?></td>
                        <td><?= htmlspecialchars($vente['seller']) ?></td>
                        <td><?= htmlspecialchars($vente['client_info']) ?></td>
                        <td><?= htmlspecialchars($vente['sale_date']) ?></td>
                        <td><?= htmlspecialchars($vente['quantity']) ?></td>
                        <td class="text-center">
                            <a href="../../public/DeleteSale.php?id=<?= $vente['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette vente ?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ajout Vente -->
<div class="modal fade" id="addSaleModal" tabindex="-1" aria-labelledby="addSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../public/CreateSale.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSaleModalLabel">Ajouter une vente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Produit</label>
                        <select class="form-select" name="product_id" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($produits as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Vendeur</label>
                        <select class="form-select" name="user_id" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($vendeurs as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="client_info" class="form-label">Client</label>
                        <input type="text" class="form-control" name="client_info" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantité</label>
                        <input type="number" class="form-control" name="quantity" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="sale_date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="sale_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rapport -->
<div class="modal fade" id="rapportModal" tabindex="-1" aria-labelledby="rapportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="get" action="rapportVentes.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rapportModalLabel">Période du rapport</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="from" class="form-label">Date de début :</label>
                    <input type="date" name="from" id="from" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="to" class="form-label">Date de fin :</label>
                    <input type="date" name="to" id="to" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Afficher le rapport</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Chart -->
<div class="modal fade" id="chartModal" tabindex="-1" aria-labelledby="chartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="get" action="chartVentes.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chartModalLabel">Période du graphique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="from" class="form-label">Date de début :</label>
                    <input type="date" name="from" id="from" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="to" class="form-label">Date de fin :</label>
                    <input type="date" name="to" id="to" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Afficher le graphique</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
