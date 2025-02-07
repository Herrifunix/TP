<?php
require_once 'Database.php';

class User {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->pdo;
    }

    public function register($nom,$email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (nom,email, password) VALUES (:nom,:email, :password)");

        return $stmt->execute(["nom"=> $nom,"email"=>$email, "password"=>$hashed_password]);
    }

    public function login($nom, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE nom = :nom");
        $stmt->execute(["nom"=>$nom]);
        $user = $stmt->fetch();

        if($user && password_verify($password,$user['password'])){
            return $user;
        }

        return false;

    }

    public function editUser($columnName, $newValue, $userName) {    
        
        $stmt = $this->pdo->prepare("UPDATE utilisateurs SET $columnName = :newValue WHERE nom = :userName");

        $stmt->execute([
            'newValue' => $newValue,
            'userName' => $userName
        ]);
    }

    public function getUserIdByName($name) {
        $sql = "SELECT id FROM utilisateurs WHERE nom = :nom"; 
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nom', $name);
        $stmt->execute();
        return $stmt->fetchColumn(); 
    }
    
}
?>