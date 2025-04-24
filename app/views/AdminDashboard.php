<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once __DIR__ . '/../bootstrap.php';
$pdo = Database::getInstance();

// Notifications
$notification = $_GET['notification'] ?? null;
$alertType = $_GET['type'] ?? 'success';

// Total produits vendus
$totalSalesQuery = $pdo->query("SELECT SUM(quantity) as total FROM sales");
$totalSold = $totalSalesQuery->fetchColumn();

// Total des commandes
$totalOrdersQuery = $pdo->query("SELECT COUNT(*) as total FROM sales");
$totalOrders = $totalOrdersQuery->fetchColumn();

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
    <title>Dashboard Admin - Stokly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card-summary {
            border-radius: 15px;
            padding: 20px;
            color: white;
            text-align: center;
            transition: 0.3s;
            border: 2px solid transparent;
            background: white;
            color: #dc3545;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.3);
        }
        .card-summary:hover {
            background-color: #dc3545;
            color: white;
            border-color: white;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.6);
        }
        .card-summary i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        table.table {
            border-radius: 12px;
            overflow: hidden;
        }
        thead th {
            font-weight: bold;
            background-color: #ffe6e6;
            color: #b30000;
            text-align: center;
        }
        td, th {
            vertical-align: middle;
            text-align: center;
        }
        .chat-bubble {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        .chat-bubble button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            font-size: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
        }

        .chat-bubble button:hover {
            background-color: white;
            color: #dc3545;
            border: 1px solid #dc3545;
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-danger px-4">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../public/assets/logo.jpg" alt="Stokly Logo" style="height: 40px; margin-right: 10px;">
        <span class="fw-bold">Stokly Admin</span>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a href="adminDashboard.php" class="nav-link active"><i class="bi bi-house me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a href="adminDashboardProduct.php" class="nav-link "><i class="bi bi-box-seam me-1"></i> Produits</a></li>
            <li class="nav-item"><a href="userGestion.php" class="nav-link"><i class="bi bi-people me-1"></i> Utilisateurs</a></li>
            <li class="nav-item"><a href="salesGestion.php" class="nav-link"><i class="bi bi-currency-dollar me-1"></i> Ventes</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <?php if ($notification): ?>
        <div class="alert alert-<?= htmlspecialchars($alertType) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($notification) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card-summary">
                <i class="bi bi-cart-check"></i>
                Total Produits Vendus<br>
                <strong><?= $totalSold ?? 0 ?></strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-summary">
                <i class="bi bi-clipboard-data"></i>
                Total Commandes<br>
                <strong><?= $totalOrders ?? 0 ?></strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-summary">
                <i class="bi bi-clipboard-data"></i>
                Objectif<br>
                <strong>45%</strong>
            </div>
        </div>

    </div>

    <?php foreach ($products as $product): ?>
        <?php if ($product['quantity'] < 50): ?>
            <div class="alert alert-warning">
                Attention : Le stock du produit <strong><?= htmlspecialchars($product['name']) ?></strong> est faible (<?= $product['quantity'] ?>).
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Chatbot Bubble -->
<div class="chat-bubble">
    <button type="button" data-bs-toggle="modal" data-bs-target="#chatModal">
        <i class="bi bi-robot me-1"></i> AI Assistance
    </button>
</div>

<!-- Chatbot Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="chatModalLabel">
                    <i class="bi bi-robot me-1"> Stocky AI</i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="chat-box" style="height: 200px; overflow-y: auto; background: #f8f8f8; padding: 10px; border-radius: 8px; margin-bottom: 10px;"></div>
                <form id="chat-form">
                    <input type="text" id="user-message" class="form-control mb-2" placeholder="Posez une question à Stocky..." required>
                    <button type="submit" class="btn btn-danger">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Le reste du HTML reste identique ici, donc inutile de le dupliquer -->



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('chat-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const input = document.getElementById('user-message');
        const message = input.value.trim();
        if (!message) return;

        const chatBox = document.getElementById('chat-box');
        chatBox.innerHTML += `<div><strong>Vous :</strong> ${message}</div>`;
        input.value = '';

        try {
            const response = await fetch('../../public/chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ message })
            });
            const reply = await response.text();
            chatBox.innerHTML += `<div><strong>Stocky :</strong> ${reply}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        } catch (error) {
            chatBox.innerHTML += `<div class="text-danger">Erreur lors de la communication avec Stocky.</div>`;
        }
    });
</script>
</body>
</html>