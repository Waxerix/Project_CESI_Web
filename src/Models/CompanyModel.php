<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class CompanyModel extends Model
{

    /**
     * Constructeur : Reçoit l'instance PDO pour les requêtes
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Insère une nouvelle entreprise dans la table 'Company'
     * @param array $data Les données provenant du formulaire
     * @return bool Succès ou échec de l'insertion
     */
    public function create($data)
    {
        // Préparation de la requête SQL
        // On utilise CURDATE() pour la colonne Create_date afin d'avoir la date du jour
        $sql = "INSERT INTO Company (Name, Email, Phone_Number, Description, Creation_date) 
                VALUES (:name, :email, :phone, :description, CURDATE())";

        $stmt = $this->pdo->prepare($sql);

        // Exécution avec protection contre les injections SQL
        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'description' => $data['description']
        ]);
    }

    /**
     * Récupère toutes les entreprises
     */
    public function getAll()
    {
        $sql = "SELECT * FROM Company ORDER BY Creation_date DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une entreprise par son ID
     * @param int $id L'identifiant de l'entreprise
     * @return bool Succès ou échec de la suppression
     */
    public function delete($id)
    {
        $sql = "SELECT ID_offer FROM Job_offer WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $jobOffers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($jobOffers as $jobOffer) {
            $sql = "DELETE FROM Wishlist WHERE ID_offer = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $jobOffer['ID_offer']]);
        }
        $sql = "DELETE FROM Evaluate WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $sql = "DELETE FROM Job_offer WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $sql = "DELETE FROM Company WHERE ID_company = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}