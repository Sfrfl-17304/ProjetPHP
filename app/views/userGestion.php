<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once __DIR__ . '/../bootstrap.php';
$pdo = Database::getInstance();
$users = $pdo->query("SELECT id, username, role FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs - Admin</title>
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
            <li class="nav-item"><a href="adminDashboard.php" class="nav-link"><i class="bi bi-house me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a href="adminDashboardProduct.php" class="nav-link "><i class="bi bi-box-seam me-1"></i> Produits</a></li>
            <li class="nav-item"><a href="userGestion.php" class="nav-link active"><i class="bi bi-people me-1"></i> Utilisateurs</a></li>
            <li class="nav-item"><a href="salesGestion.php" class="nav-link"><i class="bi bi-currency-dollar me-1"></i> Ventes</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-danger mb-4">Gestion des Utilisateurs</h1>

    <button class="btn btn-outline-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus"></i> Ajouter un utilisateur
    </button>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr><th>ID</th><th>Nom</th><th>Rôle</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td class="text-center">
                            <a href="../../public/DeleteUser.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cet utilisateur ?');">
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

<!-- Modal Ajout Utilisateur -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../public/CreateUser.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select" name="role" required>
                            <option value="">-- Choisir un rôle --</option>
                            <option value="admin">Admin</option>
                            <option value="vendeur">Vendeur</option>
                            <option value="fournisseur">Fournisseur</option>
                        </select>
                    </div>
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
