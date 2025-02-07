<?php
session_start();

require_once 'classes/User.php';
require_once 'classes/Livre.php';
$livre = new Livre();
$user = new User();

if (isset($_SESSION['nom'])) {
    $userName = $_SESSION['nom'];
    $userId = $user->getUserIdByName($userName); 
} else {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['titre'], $_POST['auteur'], $_POST['action']) && $_POST['action'] === 'ajoutLivre') {
        $titre = $_POST['titre'];
        $auteur = $_POST['auteur'];

        if (empty($titre) || empty($auteur)) {
            echo "Le titre et l'auteur sont obligatoires.";
        } else {
            $result = $livre->ajoutLivre($titre, $auteur, $userId);
            if ($result) {
                echo "Livre ajouté avec succès!";
            } else {
                echo "Erreur lors de l'ajout du livre.";
            }
        }
    }

    
    if (isset($_POST['livreId'], $_POST['userId'], $_POST['action']) && $_POST['action'] === 'remove') {
        $livreId = (int)$_POST['livreId'];
        $userId = (int)$_POST['userId'];
        $livre->supprimerDesFavoris($userId, $livreId);
    }

    
    if (isset($_POST['titre'], $_POST['action']) && $_POST['action'] === 'remove' && isset($_POST['titre'])) {
        $livre->supprimerMonLivre($_POST['titre']);
    }

    
    $updateType = $_POST['updateType'] ?? '';
    $newValue = trim($_POST['newValue'] ?? '');
    $currentUsername = $_SESSION['nom'];

    try {
        if ($updateType === 'username') {
            $user->editUser('nom', $newValue, $currentUsername);
            $_SESSION['nom'] = $newValue;
            $message = "Nom d'utilisateur mis à jour avec succès.";
        } elseif ($updateType === 'password') {
            $hashedPassword = password_hash($newValue, PASSWORD_DEFAULT);
            $user->editUser('password', $hashedPassword, $currentUsername);
            $message = "Mot de passe mis à jour avec succès.";
        } elseif ($updateType === 'email') {
            $user->editUser('email', $newValue, $currentUsername);
            $message = "Email mis à jour avec succès.";
        }
    } catch (PDOException $e) {
        $message = "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../TP/CSS/styles.css">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bienvenue <?php echo htmlspecialchars($_SESSION['nom']); ?></h1>
    <a href="index.php">Accueil</a>
    <p><a href="logout.php">Déconnexion</a></p>

    <h2>Mes Favoris</h2>
    <h3>Vous avez 
        <?php 
        $counter = $livre->selectCountFavLivre($userId); 
        echo $counter;
        ?> 
        favoris
    </h3>

    <?php
    $livres = $livre->getAllLivres();  
    foreach ($livres as $l) {
        
        $Favlivres = $livre->selectFromFavorites($l['id'], $userId);
        foreach ($Favlivres as $fav) {
            echo "Title: " . htmlspecialchars($fav['titre']) . ", Auteur: " . htmlspecialchars($fav['auteur']) . "<br>";
            echo "<form action='dashboard.php' method='POST'>
                <input type='hidden' name='livreId' value='" . $l['id'] . "'>
                <input type='hidden' name='userId' value='" . $userId . "'>
                <button type='submit' name='action' value='remove'>Retirer des favoris</button>
            </form><br>";
        }
    }
    ?>

    <h2>Mes livres</h2>
    <?php
    $userLivre = $livre->selectMesLivres($userId);
    foreach ($userLivre as $l) {
        echo "Title: " . htmlspecialchars($l['titre']) . ", Auteur: " . htmlspecialchars($l['auteur']) . "<br>";
        echo "<form action='dashboard.php' method='POST'>
            <input type='hidden' name='titre' value='" . $l['titre'] . "'> <!-- Sending book ID -->
            <button type='submit' name='action' value='remove'>Supprimer le livre</button>
        </form><br>";
    }
    ?>

    <h2>Paramètres</h2>
    <form action="dashboard.php" method="POST">
        <label>Choisissez ce que vous souhaitez modifier:</label>
        <select name="updateType" required>
            <option disabled value="">Sélectionnez une option</option>
            <option value="username">Nom d'utilisateur</option>
            <option value="password">Mot de passe</option>
            <option value="email">Email</option>
        </select>
        <input type="text" id="newValue" name="newValue" placeholder="" required>
        <input type="submit" value="Mettre à jour">
    </form>

    <h2>Ajouter un livre</h2>
    <form action="dashboard.php" method="POST">
        <input type="text" name="titre" placeholder="Titre du livre" required>
        <input type="text" name="auteur" placeholder="Auteur" required>
        <button type="submit" name="action" value="ajoutLivre">Ajouter le livre</button>
    </form>
</body>
</html>
