<?php
require_once '../vendor/autoload.php';
require_once '../app/bootstrap.php';

use Dompdf\Dompdf;

$id = $_GET['id'] ?? null;
if (!$id) die("ID de vente manquant");

$pdo = Database::getInstance();
$stmt = $pdo->prepare("
    SELECT s.*, p.name AS product_name, p.price, u.username AS vendeur
    FROM sales s
    JOIN products p ON p.id = s.product_id
    JOIN users u ON u.id = s.user_id
    WHERE s.id = ?
");
$stmt->execute([$id]);
$vente = $stmt->fetch();

if (!$vente) die("Vente introuvable");

$total = $vente['quantity'] * $vente['price'];

ob_start();
?>

    <h1 style="color:#d32f2f;"> Facture #<?= $vente['id'] ?></h1>
    <p><strong>Client :</strong> <?= $vente['client_info'] ?></p>
    <p><strong>Produit :</strong> <?= $vente['product_name'] ?></p>
    <p><strong>Quantité :</strong> <?= $vente['quantity'] ?></p>
    <p><strong>Prix :</strong> <?= $vente['price'] ?> MAD</p>
    <p><strong>Date :</strong> <?= $vente['sale_date'] ?></p>
    <p><strong>Vendeur :</strong> <?= $vente['vendeur'] ?></p>

    <hr>
    <p><strong>Total :</strong> <?= number_format($total, 2) ?> MAD</p>

<?php
$html = ob_get_clean();
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream("facture-vente-{$vente['id']}.pdf", ["Attachment" => false]);
exit;
