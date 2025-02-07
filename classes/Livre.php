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

    public function getAllLivres() {
        return $this->pdo->query("SELECT * FROM livres")->fetchAll();
    }

    public function ajoutLivre($titre, $auteur, $userId) {
        if (!is_string($titre) || !is_string($auteur) || !is_int($userId) || empty($titre) || empty($auteur)) {
            return false;
        }
        $sql = "INSERT INTO livres (titre, auteur, utilisateur_id) VALUES (:titre, :auteur, :userId)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titre' => htmlspecialchars(trim($titre)),
            ':auteur' => htmlspecialchars(trim($auteur)),
            ':userId' => $userId
        ]);
    }

    public function supprimerMonLivre($titre, $userId) {
        if (!is_string($titre) || empty($titre) || !is_int($userId)) {
            return false;
        }
        $sql = "DELETE FROM livres WHERE titre = :titre AND utilisateur_id = :userId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titre' => htmlspecialchars(trim($titre)),
            ':userId' => $userId
        ]);
    }

    public function ajoutAuxFavoris($livreId, $userId) {
        $sql = "INSERT INTO favoris (livre_id, utilisateur_id) SELECT :livreId, :userId WHERE NOT EXISTS (SELECT 1 FROM favoris WHERE livre_id = :livreId AND utilisateur_id = :userId)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':livreId' => $livreId, ':userId' => $userId]);
    }

    public function supprimerDesFavoris($userId, $livreId) {
        $sql = "DELETE FROM favoris WHERE utilisateur_id = :userId AND livre_id = :livreId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':userId' => $userId, ':livreId' => $livreId]);
    }

    public function selectFromFavorites($livreId, $userId) {
        $sql = "SELECT l.titre, l.auteur FROM favoris f INNER JOIN livres l ON f.livre_id = l.id WHERE f.livre_id = :livreId AND f.utilisateur_id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':livreId' => $livreId, ':userId' => $userId]);
        return $stmt->fetchAll();
    }

    public function selectMesLivres($userId) {
        $sql = "SELECT * FROM livres WHERE utilisateur_id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll();
    }

    public function selectCountFavLivre($userId) {
        $sql = "SELECT COUNT(*) FROM favoris WHERE utilisateur_id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchColumn();
    }

    public function searchLivre($searchTerm) {
        if (!is_string($searchTerm) || empty(trim($searchTerm))) {
            return [];
        }
        $searchTerm = trim($searchTerm);
        $searchTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');

        $sql = "SELECT * FROM livres WHERE titre LIKE :searchTerm OR auteur LIKE :searchTerm LIMIT 20";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':searchTerm' => "%{$searchTerm}%"]);
        return $stmt->fetchAll();
    }

    
}
