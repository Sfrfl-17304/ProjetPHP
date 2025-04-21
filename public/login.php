<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-container">
    <img src="assets/logo.jpg" class="logo" alt="">
    <h2>Connexion à Stokly</h2>
    <div class="slogan">Where stock meets smart.</div>
    <?php if(isset($_SESSION['login_error'])): ?>
        <div class="error"><?= $_SESSION['login_error'] ?></div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <form method="POST" action="Auth.php">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>