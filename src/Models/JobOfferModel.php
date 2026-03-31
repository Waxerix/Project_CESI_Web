<?php
namespace App\Models;
use App\Core\Model;
use PDO;
class JobOfferModel extends Model{
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function getAllOffers() {
        
        $sql = "SELECT j.ID_offer, j.Category, j.Title, j.Duration, j.Salary, c.Name as CompanyName 
                FROM Job_offer j 
                LEFT JOIN Company c ON j.ID_company = c.ID_company 
                ORDER BY j.ID_offer DESC"; 

        $stmt = $this->pdo->query($sql);
        
       
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getOfferById($id) {
        $sql = "SELECT j.ID_offer, j.Title, j.Description, j.Duration, j.Salary, c.Name as CompanyName 
                FROM Job_offer j 
                LEFT JOIN Company c ON j.ID_company = c.ID_company 
                WHERE j.ID_offer = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBestOffer($limit = 5){

        $sql = "SELECT j.ID_offer, j.Category, j.Title, j.Duration, j.Salary, c.Name as CompanyName, AVG(e.Rate) as average_rating
                FROM Job_offer j
                JOIN Company c ON j.ID_company = c.ID_company
                JOIN Evaluate e ON c.ID_company = e.ID_company
                GROUP BY j.ID_offer, j.Category, j.Title, j.Duration, j.Salary, c.Name 
                ORDER BY average_rating DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);


    }
}
