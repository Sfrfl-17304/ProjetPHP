<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accès non autorisé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1e1e1e;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .card {
            background-color: #2c2c2c;
            border: 1px solid #b71c1c;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            max-width: 500px;
        }

        .btn {
            margin-top: 20px;
            background-color: #d32f2f;
            color: white;
        }

        .btn:hover {
            background-color: #b71c1c;
        }

        h1 {
            color: #ff4d4d;
            font-size: 2rem;
        }

        p {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<div class="card">
    <h1> Accès non autorisé</h1>
    <p>Vous n'avez pas la permission d'accéder à cette page sans vous connecter.</p>
    <a href="../../public/login.php" class="btn">Retour à la page de connexion</a>
</div>

</body>
</html>
