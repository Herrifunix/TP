<?php
session_start();
require "classes/Livre.php";
require_once 'classes/User.php';
if (isset($_SESSION['nom'])) {
$userName = $_SESSION['nom'];
$user = new User();
$userId = $user->getUserIdByName($userName); 
}
else{
    $userName = NULL;
    $userId=0;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['livreId'], $_POST['userId'], $_POST['action'])) {
        $livreId = (int)$_POST['livreId'];
        $userId = (int)$_POST['userId'];
        $action = $_POST['action'];

        $livre = new Livre();

        if ($action === 'add') {
            $result = $livre->ajoutAuxFavoris($livreId, $userId);
        } elseif ($action === 'remove') {
            $result = $livre->supprimerDesFavoris($userId, $livreId);
        }

        
        if ($result) {
            echo "Opération réussie pour l'ID: $livreId et l'utilisateur ID: $userId.";
        } else {
            echo "Échec de l'opération pour l'ID: $livreId et l'utilisateur ID: $userId.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    



    <?php if (isset($_SESSION['nom'])) { ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="gestion.php">Gestion des livres</a>
        <a href="logout.php">Déconnexion</a>
    <?php } else { ?>
        <a href="register.php">Inscription</a>
        <a href="login.php">Connexion</a>
    <?php } ?>
    <br>
    <link rel="stylesheet" href="../TP/CSS/styles.css">
</head>

<body>
<h1>Bienvenue <?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : ''; ?></h1>
    <?php
    $livre = new Livre();  
    $livres = $livre->getAllLivres(); 

    foreach ($livres as $l) {
        
        echo "Title: " . htmlspecialchars($l['titre']) . ", Auteur: " . htmlspecialchars($l['auteur']);

        
        echo "<form action='index.php' method='POST'>
             <input type='hidden' name='livreId' value='" . $l['id'] . "'>
             <input type='hidden' name='userId' value='" . $userId . "'>
             <button type='submit' name='action' value='add'>Ajouter aux favoris</button>
           </form> <br>";


        echo "<form action='index.php' method='POST'>
        <input type='hidden' name='livreId' value='" . $l['id'] . "'>
        <input type='hidden' name='userId' value='" . $userId . "'>
        <button type='submit' name='action' value='remove'>Retirer des favoris</button>
      </form> <br>";
    }




    ?>

    <h2>Mes Favoris</h2>
    <h3>Vous avez 
        <?php 
        $counter = $livre->selectCountFavLivre($userId);
        echo $counter;
        ?> 
        favoris</h3>
    <?php
    
    $livres = $livre->getAllLivres();  

    foreach ($livres as $l) {
        
        $Favlivres = $livre->selectFromFavorites($l['id'], $userId);
        foreach ($Favlivres as $fav) {
            echo "Title: " . htmlspecialchars($fav['titre']) . ", Auteur: " . htmlspecialchars($fav['auteur']) . "<br>";
        }
    }

    ?>


    


</body>

</html>