<?php
require_once '../vendor/autoload.php';
require_once '../app/bootstrap.php';

use Dompdf\Dompdf;

$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

if (!$from || !$to) die("Dates manquantes");

$pdo = Database::getInstance();
$stmt = $pdo->prepare("
    SELECT s.*, p.name AS product_name, p.price, u.username AS vendeur
    FROM sales s
    JOIN products p ON p.id = s.product_id
    JOIN users u ON u.id = s.user_id
    WHERE s.sale_date BETWEEN ? AND ?
");
$stmt->execute([$from, $to]);
$ventes = $stmt->fetchAll();

ob_start();
?>

    <h1 style="text-align:center;color:#d32f2f;"> Rapport des ventes</h1>
    <p>Période : <?= $from ?> au <?= $to ?></p>

    <table width="100%" border="1" cellspacing="0" cellpadding="5">
        <tr>
            <th>ID</th><th>Produit</th><th>Quantité</th><th>Prix</th><th>Client</th><th>Vendeur</th><th>Date</th><th>Total</th>
        </tr>
        <?php $total = 0; ?>
        <?php foreach ($ventes as $v): $sousTotal = $v['quantity'] * $v['price']; $total += $sousTotal; ?>
            <tr>
                <td><?= $v['id'] ?></td>
                <td><?= $v['product_name'] ?></td>
                <td><?= $v['quantity'] ?></td>
                <td><?= $v['price'] ?> MAD</td>
                <td><?= $v['client_info'] ?></td>
                <td><?= $v['vendeur'] ?></td>
                <td><?= $v['sale_date'] ?></td>
                <td><?= number_format($sousTotal, 2) ?> MAD</td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Total : <?= number_format($total, 2) ?> MAD</strong></p>

<?php
$html = ob_get_clean();
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream("rapport-ventes.pdf", ["Attachment" => false]);
exit;
