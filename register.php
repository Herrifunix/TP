<?php
session_start();
require_once 'classes/User.php';

$user = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = htmlspecialchars($_POST['nom']);
    $password = htmlspecialchars($_POST['password']);
    $email = htmlspecialchars($_POST['email']);


    if (empty($nom) || empty($password) || empty($email)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        if ($user->register($nom, $email, $password)) #{

            $succes = 'Inscription réussie ! Vous pouvez vous connecter.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>

<body>
    <h1>Inscription</h1>
    <?php if (isset($error)) echo "<p style='color: red;'>$error </p>" ?>
    <?php if (isset($succes)) echo "<p style='color: green;'>$succes </p>" ?>
    <form action="register.php" method="post">
        <input type="text" name="nom" placeholder="Nom d'utilisateur" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">S'inscrire</button>
    </form>

    <a href="index.php">Retour à l'acceuil</a>
</body>

</html>