<?php
session_start();
require_once 'classes/User.php';
$user = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = htmlspecialchars($_POST['nom']);
    $password = htmlspecialchars($_POST['password']);


    if (empty($nom) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $loggedInUser = $user->login($nom, $password);
        if ($loggedInUser) {
            $_SESSION['nom'] = $loggedInUser['nom'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>

<body>
    <h1>Connexion</h1>
    <?php if (isset($error)) echo "<p style='color: red;'>$error </p>" ?>
    <form action="login.php" method="post">
        <input type="text" name="nom" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>

    <a href="index.php">Retour à l'acceuil</a>
</body>

</html>