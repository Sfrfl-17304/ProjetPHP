<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once __DIR__ . '/../bootstrap.php';
$pdo = Database::getInstance();

$fournisseurId = $_GET['fournisseur_id'] ?? null;
$params = [];

$sql = "
    SELECT p.id, p.name, p.description, p.quantity, p.price, p.image,
           c.name AS category_name,
           u.username AS supplier_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.supplier_id = u.id
";

if (!empty($fournisseurId)) {
    $sql .= " WHERE p.supplier_id = ?";
    $params[] = $fournisseurId;
}

$sql .= " ORDER BY p.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$fournisseurs = $pdo->query("SELECT id, username FROM users WHERE role = 'fournisseur'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Produits - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
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
        .btn-outline-success:hover, .btn-outline-danger:hover {
            color: white;
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
            <li class="nav-item"><a href="adminDashboard.php" class="nav-link "><i class="bi bi-house me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a href="adminDashboardProduct.php" class="nav-link active"><i class="bi bi-box-seam me-1"></i> Produits</a></li>
            <li class="nav-item"><a href="userGestion.php" class="nav-link"><i class="bi bi-people me-1"></i> Utilisateurs</a></li>
            <li class="nav-item"><a href="salesGestion.php" class="nav-link"><i class="bi bi-currency-dollar me-1"></i> Ventes</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-danger mb-4">Gestion des Produits</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET">
            <label for="fournisseur_id" class="form-label">Filtrer par fournisseur :</label>
            <select name="fournisseur_id" id="fournisseur_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tous les fournisseurs --</option>
                <?php foreach ($fournisseurs as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= ($fournisseurId == $f['id']) ? 'selected' : '' ?>><?= htmlspecialchars($f['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle"></i> Ajouter un produit
        </button>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Prix</th>
                    <th>Catégorie</th>
                    <th>Fournisseur</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><img src="../../public/uploads/<?= $p['image'] ?>" alt="Image" width="50"></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['description']) ?></td>
                        <td><?= htmlspecialchars($p['quantity']) ?></td>
                        <td><?= htmlspecialchars($p['price']) ?> MAD</td>
                        <td><?= htmlspecialchars($p['category_name']) ?></td>
                        <td><?= htmlspecialchars($p['supplier_name']) ?></td>
                        <td class="text-center">
                            <a href="../../public/DeleteProduct.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce produit ?');">
                                <i class="bi bi-trash"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#stockModal<?= $p['id'] ?>">
                                <i class="bi bi-plus"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Stock -->
                    <div class="modal fade" id="stockModal<?= $p['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../../public/AddStock.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Ajouter du stock pour <?= htmlspecialchars($p['name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <label for="quantity" class="form-label">Quantité à ajouter</label>
                                        <input type="number" class="form-control" name="quantity" required min="1">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Ajouter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div>
        <a href="dashboardTri.php" class="btn btn-danger">
            <i class="bi bi-box-seam me-1"></i> Trier par Stock
        </a></div>

    </div>

    <hr class="my-5">
    <h1 class="text-danger mb-4">Gestion des Catégories</h1>
    <button class="btn btn-outline-success mb-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-tags"></i> Ajouter une catégorie
    </button>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr><th>ID</th><th>Nom</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td class="text-center">
                            <a href="../../public/DeleteCategory.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette catégorie ?');">
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

<!-- Modal Ajouter Produit -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../public/CreateProduct.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" name="name" placeholder="Nom" required>
                    <textarea class="form-control mb-3" name="description" placeholder="Description" rows="3"></textarea>
                    <input type="number" class="form-control mb-3" name="quantity" placeholder="Quantité" required>
                    <input type="number" step="0.01" class="form-control mb-3" name="price" placeholder="Prix" required>
                    <input type="file" class="form-control mb-3" name="image" accept="image/*">
                    <select class="form-select mb-3" name="category_id" required>
                        <option value="">-- Choisir une catégorie --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select" name="supplier_id" required>
                        <option value="">-- Choisir un fournisseur --</option>
                        <?php foreach ($fournisseurs as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajouter Catégorie -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../public/CreateCategory.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" name="name" placeholder="Nom de la catégorie" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
