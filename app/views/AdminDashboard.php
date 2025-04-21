<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stokly Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../public/css/admin.css">

</head>
<body>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
    <h1>Admin Dashboard</h1>
    <p class="slogan">Where stock meets smart.</p>
</header>

<main class="dashboard">
    <section class="card ">
        <div class="card-header">

        <h2>Utilisateurs</h2>
        <!-- Button trigger modal -->
        <button type="button"   class="btn btn-outline-success btn-sm btn-sm-custom"
                data-bs-toggle="modal" data-bs-target="#addUserModal">
            Ajouter
        </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="../../public/CreateUser.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Créer un utilisateur</h5>
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
                                    <option value="">--Choisir un rôle--</option>
                                    <option value="admin">Admin</option>
                                    <option value="vendeur">Vendeur</option>
                                    <option value="fournisseur">Fournisseur</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-success">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table>
            <thead>
            <tr><th>ID</th><th>Nom</th><th>Rôle</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php
            $host = 'sql7.freesqldatabase.com';
            $db   = 'sql7774461';
            $user = 'sql7774461';
            $pass = 'rqxhprUNxK';
            $charset = 'utf8mb4';
            $dsn = "mysql:host=$host;dbname=$db;port=3306;charset=$charset";

            try {
                $pdo = new PDO($dsn, $user, $pass);
                $stmt = $pdo->query("SELECT id, username, role FROM users");
                while ($row = $stmt->fetch()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['role']}</td> <td style='text-align:center;'>
                <button class='delete-btn' onclick=\"if(confirm('Supprimer cet utilisateur ?')) window.location.href='../../public/DeleteUser.php?id={$row['id']}'\">
                        <i class='bi bi-trash'></i>
                </button>
            </td></tr>";
                }
            } catch (PDOException $e) {
                echo '<tr><td colspan="3">Erreur : ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <div class="card-header">
            <h2>Produits</h2>

            <a href="dashboardTri.php" class="btn btn-danger">
                <i class="bi bi-box-seam me-1"></i> Tri
            </a>

            <button
                    type="button"
                    class="btn btn-outline-success btn-sm btn-sm-custom"
                    data-bs-toggle="modal"
                    data-bs-target="#addProductModal"
            >
                Ajouter
            </button>
        </div>
        <!-- Add Product Modal -->
        <!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="../../public/CreateProduct.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProductModalLabel">Ajouter un produit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantité</label>
                                <input type="number" class="form-control" name="quantity" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Prix</label>
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Catégorie</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">-- Choisir une catégorie --</option>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT id, name FROM categories");
                                        while ($row = $stmt->fetch()) {
                                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<option disabled>Erreur : " . htmlspecialchars($e->getMessage()) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Fournisseur</label>
                                <select class="form-select" name="supplier_id" required>
                                    <option value="">-- Choisir un fournisseur --</option>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'fournisseur'");
                                        while ($row = $stmt->fetch()) {
                                            echo "<option value='{$row['id']}'>{$row['username']}</option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<option disabled>Erreur : " . htmlspecialchars($e->getMessage()) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-success">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <form method="GET" class="mb-3">
            <label for="fournisseur_id" class="form-label">Filtrer par fournisseur :</label>
            <select name="fournisseur_id" id="fournisseur_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tous les fournisseurs --</option>
                <?php
                $fournisseurs = $pdo->query("SELECT id, username FROM users WHERE role = 'fournisseur'");
                foreach ($fournisseurs as $f) {
                    $selected = (isset($_GET['fournisseur_id']) && $_GET['fournisseur_id'] == $f['id']) ? 'selected' : '';
                    echo "<option value='{$f['id']}' $selected>{$f['username']}</option>";
                }
                ?>
            </select>
        </form>


        <table>
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
            <?php
            $fournisseurId = $_GET['fournisseur_id'] ?? null;

            $sql = "
    SELECT p.id, p.name, p.description, p.quantity, p.price, p.image,
           c.name AS category_name,
           u.username AS supplier_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.supplier_id = u.id
";

            $params = [];

            if (!empty($fournisseurId)) {
                $sql .= " WHERE p.supplier_id = ?";
                $params[] = $fournisseurId;
            }

            $sql .= " ORDER BY p.id DESC";

            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $products = $stmt->fetchAll();

                foreach ($products as $row) {
                    echo "<tr>
            <td>{$row['id']}</td>
            <td>";
                    if (!empty($row['image'])) {
                        echo "<img src='../../public/uploads/{$row['image']}' alt='Produit' style='max-width: 50px; max-height: 50px;'>";
                    } else {
                        echo "—";
                    }
                    echo "</td>
            <td>{$row['name']}</td>
            <td>{$row['description']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['price']}</td>
            <td>{$row['category_name']}</td>
            <td>{$row['supplier_name']}</td>
            <div class='d-flex justify-content-center gap-2'>
            <td style='text-align:center;'>
                <button class='delete-btn' onclick=\"if(confirm('Supprimer ce produit ?')) window.location.href='../../public/DeleteProduct.php?id={$row['id']}'\">
                    <i class='bi bi-trash'></i>
                </button>
                
                <button type='button'
                    class='btn btn-sm btn-outline-success ms-1'
                    data-bs-toggle='modal'
                    data-bs-target='#addStockModal{$row['id']}'>
                <i class='bi bi-plus'></i>
            </button>
            </td>
            </div>
        </tr>";

                    echo "
    <div class='modal fade' id='addStockModal{$row['id']}' tabindex='-1' aria-labelledby='addStockLabel{$row['id']}' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <form action='../../public/AddStock.php' method='POST'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='addStockLabel{$row['id']}'>Ajouter du stock – " . htmlspecialchars($row['name']) . "</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Fermer'></button>
                    </div>
                    <div class='modal-body'>
                        <input type='hidden' name='product_id' value='{$row['id']}'>
                        <div class='mb-3'>
                            <label for='quantity{$row['id']}' class='form-label'>Quantité à ajouter :</label>
                            <input type='number' class='form-control' name='quantity' id='quantity{$row['id']}' required min='1'>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='submit' class='btn btn-success'>Ajouter</button>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>";
                }

            } catch (PDOException $e) {
                echo '<tr><td colspan="9">Erreur : ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
            </tbody>

        </table>

    </section>

    <section class="card">
        <section class="card">
            <div class="card-header">
                <h2>Catégories</h2>
                <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    Ajouter
                </button>
            </div>

            <!-- Modal Ajouter Catégorie -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="../../public/CreateCategory.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCategoryModalLabel">Ajouter une catégorie</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="category_name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-success">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <table>
            <thead>
            <tr><th>ID</th><th>Nom</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php
            try {
                $stmt = $pdo->query("SELECT id, name FROM categories");
                while ($row = $stmt->fetch()) {
                    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td style='text-align:center;'>
                <button class='delete-btn' onclick=\"if(confirm('Supprimer cette catégorie ?')) window.location.href='../../public/DeleteCategory.php?id={$row['id']}'\">
                    <i class='bi bi-trash'></i>
                </button>
            </td>
          </tr>";
                }
            } catch (PDOException $e) {
                echo '<tr><td colspan="2">Erreur : ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </section>

    <section class="card full-width">
        <div class="card-header">
            <h2>Ventes</h2>
        <!-- Button to open modal -->
        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSaleModal">
            Ajouter
        </button>
        </div>

        <!-- Modal Ajouter Vente -->
        <div class="modal fade" id="addSaleModal" tabindex="-1" aria-labelledby="addSaleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="../../public/CreateSale.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addSaleModalLabel">Nouvelle vente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Produit</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">-- Choisir un produit --</option>
                                    <?php
                                    $products = $pdo->query("SELECT id, name FROM products");
                                    while ($row = $products->fetch()) {
                                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vendeur</label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">-- Choisir un vendeur --</option>
                                    <?php
                                    $users = $pdo->query("SELECT id, username FROM users WHERE role='vendeur'");
                                    while ($row = $users->fetch()) {
                                        echo "<option value='{$row['id']}'>{$row['username']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Client</label>
                                <input type="text" name="client_info" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantité</label>
                                <input type="number" name="quantity" class="form-control" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="sale_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Valider</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table>
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
            <?php
            try {
                $stmt = $pdo->query("
                SELECT s.id, s.client_info, s.sale_date, s.quantity,
                       p.name AS product_name,
                       u.username AS seller
                FROM sales s
                LEFT JOIN products p ON s.product_id = p.id
                LEFT JOIN users u ON s.user_id = u.id
                ORDER BY s.sale_date DESC
            ");

                while ($row = $stmt->fetch()) {
                    echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['seller']}</td>
                    <td>{$row['client_info']}</td>
                    <td>{$row['sale_date']}</td>
                    <td>{$row['quantity']}</td>
                    <td style='text-align:center;'>
                        <button class='delete-btn' onclick=\"if(confirm('Supprimer cette vente ?')) window.location.href='../../public/DeleteSale.php?id={$row['id']}'\">
                            <i class='bi bi-trash'></i>
                        </button>
                    </td>
                </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='7'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </section>
</main>
<!-- Bootstrap JS (for modal functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>