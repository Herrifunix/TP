<?php
session_start();
require_once 'classes/Livre.php'; 

$livre = new Livre();
$message = "";
$userId = $_SESSION['userId'];

// Ajout d'un livre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $titre = trim($_POST['titre']);
    $auteur = trim($_POST['auteur']);

    if (!empty($titre) && !empty($auteur)) {
        if ($livre->ajoutLivre($titre, $auteur, $userId)) {
            $message = "✅ Livre ajouté avec succès !";
        } else {
            $message = "❌ Erreur lors de l'ajout du livre.";
        }
    } else {
        $message = "❌ Veuillez remplir tous les champs.";
    }
}

// Suppression d'un livre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    $titre = trim($_POST['titre_suppression']);

    if (!empty($titre)) {
        if ($livre->supprimerMonLivre($titre)) {
            $message = "✅ Livre supprimé avec succès !";
        } else {
            $message = "❌ Erreur lors de la suppression du livre.";
        }
    } else {
        $message = "❌ Veuillez sélectionner un livre.";
    }
}

// Récupération des livres existants
$livres = $livre->getAllLivres();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: auto; }
        form { margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; }
        button { cursor: pointer; }
    </style>
</head>
<body>
<a href="index.php">Accueil</a>
<a href="dashboard.php">Dashboard</a>
<a href="logout.php">Déconnexion</a>
    <h1>📚 Gestion des Livres</h1>

    <?php if ($message): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <form method="POST">
        <h2>Ajouter un livre</h2>
        <label>Titre :</label>
        <input type="text" name="titre" required>
        <br>
        <label>Auteur :</label>
        <input type="text" name="auteur" required>
        <br>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <form method="POST">
        <h2>Supprimer un livre</h2>
        <label>Choisir un livre :</label>
        <select name="titre_suppression" required>
            <option value="">Sélectionner un livre</option>
            <?php foreach ($livres as $livre): ?>
                <option value="<?= htmlspecialchars($livre['titre']) ?>">
                    <?= htmlspecialchars($livre['titre']) ?> - <?= htmlspecialchars($livre['auteur']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <button type="submit" name="supprimer">Supprimer</button>
    </form>

    <h2>📖 Liste des livres</h2>
    <ul>
        <?php foreach ($livres as $livre): ?>
            <li><?= htmlspecialchars($livre['titre']) ?> (<?= htmlspecialchars($livre['auteur']) ?>)</li>
        <?php endforeach; ?>
    </ul>

</body>
</html>
