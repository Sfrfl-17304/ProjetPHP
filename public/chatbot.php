<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $userMessage = trim($_POST['message']);

    if (empty($userMessage)) {
        echo "Message vide.";
        exit;
    }

    $apiKey = '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'; // Remplace par ta clé OpenRouter
    $endpoint = 'https://openrouter.ai/api/v1/chat/completions';

    $headers = [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ];

    $data = [
        "model" => "meta-llama/llama-4-maverick:free", // Modèle gratuit supporté par OpenRouter
        "messages" => [
            ["role" => "system", "content" => "Tu es Stocky, un assistant IA intégré à l'application web Stokly, destinée à la gestion des stocks et des ventes. Tu aides uniquement sur cette application. Voici ce que tu sais :

L'application utilise PHP avec une architecture MVC, une base de données MySQL, et du HTML/CSS/JS côté client. Elle comporte les interfaces suivantes :

1. **AdminDashboard** :
   - Affiche des cartes résumant les ventes, commandes et objectifs.
   - Possède une navbar avec navigation vers la gestion des produits, utilisateurs et ventes.
   - Notifications s'affichent si stock < 50.
   - Intègre un chatbot nommé Stocky en bas à droite.

2. **adminDashboardProduct.php** :
   - Affiche une table de produits avec image, prix, quantité, catégorie, fournisseur.
   - Permet d'ajouter un produit via modal.
   - Filtre par fournisseur.
   - Ajout de stock via bouton modal.
   - Gestion des catégories dans la même page.

3. **userGestion.php** :
   - Permet d’ajouter, voir et supprimer des utilisateurs (admin, vendeur, fournisseur).

4. **salesGestion.php** :
   - Liste des ventes.
   - Génération de rapports PDF selon une période ou un client.
   - Affichage de graphiques de vente.

5. **supplierHomePage.php** :
   - Affiche uniquement les produits du fournisseur connecté.
   - Interface simplifiée.

6. **vendorHomePage.php** :
   - Permet d’enregistrer une vente.
   - Ne montre que les ventes faites par ce vendeur.

Tu dois toujours :
- Répondre uniquement à des questions liées à Stokly.
- Ne jamais répondre à des questions personnelles ou hors sujet.
- Répondre de façon claire, utile et concise en français.

Tu es expert de cette application, et ton rôle est d’assister l’utilisateur dans l’utilisation ou la compréhension de l’interface ou des fonctionnalités de Stokly."],
            ["role" => "user", "content" => $userMessage]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Erreur Curl : " . curl_error($ch);
    } else {
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status === 200) {
            $decoded = json_decode($response, true);
            $reply = $decoded['choices'][0]['message']['content'] ?? "(Pas de réponse obtenue)";
            echo nl2br(htmlspecialchars($reply));
        } else {
            echo "Erreur OpenRouter (code $status) : " . htmlspecialchars($response);
        }
    }

    curl_close($ch);
} else {
    echo "Requête invalide.";
}
