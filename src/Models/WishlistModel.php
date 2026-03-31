<?php
namespace App\Models;

use PDO;

// On ajoute "extends Model" pour la cohérence
class WishlistModel extends Model {
    
    // On utilise $db pour la connexion
    protected $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }
    
    public function getAllByUserId($id_user) {
        $sql = "SELECT o.* FROM Job_offer o 
                JOIN Wishlist w ON o.ID_offer = w.ID_offer 
                WHERE w.ID_user = :id_user";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($id_user, $id_offer) {
        $sql = "INSERT IGNORE INTO Wishlist (ID_user, ID_offer) VALUES (:id_user, :id_offer)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_user' => $id_user, 
            'id_offer' => $id_offer
        ]);
    }

    public function remove($id_user, $id_offer) {
        $sql = "DELETE FROM Wishlist WHERE ID_user = :id_user AND ID_offer = :id_offer";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_user' => $id_user, 
            'id_offer' => $id_offer
        ]);
    }
}