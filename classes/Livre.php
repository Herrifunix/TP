<?php
require_once 'Database.php';

class Livre
{
    private $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->pdo;
    }

    public function getAllLivres()
    {

        $stmt = $this->pdo->prepare("SELECT * FROM livres");


        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajoutAuxFavoris($livreId, $userId)
    {

        $sql = "SELECT * FROM favoris WHERE livre_id = :livreId AND utilisateur_id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':livreId', $livreId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);


        if ($stmt->execute()) {
            $existingFavorite = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($existingFavorite) {
                echo "This book is already in your favorites.";
                return false;
            } else {

                $sql = "INSERT INTO favoris (livre_id, utilisateur_id) VALUES (:livreId, :userId)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':livreId', $livreId, PDO::PARAM_INT);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);


                if ($stmt->execute()) {
                    echo "The book has been added to your favorites!";
                    return true;
                } else {
                    echo "There was an error adding the book to your favorites.";
                    return false;
                }
            }
        } else {
            echo "Error checking the favorites list.";
            return false;
        }
    }




    public function supprimerDesFavoris($auteur, $titre)
    {

        $sql = "DELETE FROM favoris WHERE utilisateur_id = :user AND livre_id = :livre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user', $auteur, PDO::PARAM_INT);
        $stmt->bindParam(':livre', $titre, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "Livre numéro $titre a été retiré des favoris de l'utilisateur $auteur avec succès !";
            return true;
        } else {
            echo "Échec de la suppression du livre numéro $titre des favoris de l'utilisateur $auteur. Erreur: " . implode(" ", $stmt->errorInfo());
            return false;
        }
    }



    public function selectFromFavorites($titre, $auteur)
    {

        $sql = "SELECT l.titre, l.auteur, f.id, f.utilisateur_id, f.livre_id
                FROM favoris f
                INNER JOIN livres l ON f.livre_id = l.id
                WHERE f.livre_id = :titre AND f.utilisateur_id = :auteur";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':titre', $titre, PDO::PARAM_INT);
        $stmt->bindParam(':auteur', $auteur, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } else {
            echo "Query failed: " . htmlspecialchars(print_r($stmt->errorInfo(), true));
            return false;
        }
    }

    public function ajoutLivre($titre, $auteur, $userId)
    {

        $sql = "INSERT INTO livres (titre, auteur, utilisateur_id) VALUES (:titre, :auteur, :utilisateur_id)";
        $stmt = $this->pdo->prepare($sql);


        $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
        $stmt->bindParam(':auteur', $auteur, PDO::PARAM_STR);
        $stmt->bindParam(':utilisateur_id', $userId, PDO::PARAM_INT);


        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erreur: " . implode(" ", $stmt->errorInfo());
            return false;
        }
    }

    public function supprimerMonLivre($titre)
    {

        $sql = "DELETE FROM livres WHERE titre=:titre";
        $stmt = $this->pdo->prepare($sql);


        $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);


        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erreur: " . implode(" ", $stmt->errorInfo());
            return false;
        }
    }


    public function selectMesLivres($userId)
    {
        $sql = "SELECT * FROM livres WHERE utilisateur_id = :userId";

        $stmt = $this->pdo->prepare($sql);


        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } else {
            echo "Query failed: " . htmlspecialchars(print_r($stmt->errorInfo(), true));
            return false;
        }
    }

    

    public function selectCountFavLivre($userId) {
        
        $sql = "SELECT COUNT(*) FROM favoris WHERE utilisateur_id = :userId";
        
        
        $stmt = $this->pdo->prepare($sql);
        
        
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        
        if ($stmt->execute()) {
            
            $count = $stmt->fetchColumn();
            return $count; 
        } else {
            
            echo "Query failed: " . htmlspecialchars(print_r($stmt->errorInfo(), true));
            return false;
        }
    }
    
}
