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

    public function searchAndFilterOffers(string $keyword = '', string $category = '', string $minSalary = '', string $duration = '') {
        
        $sql = "SELECT j.ID_offer, j.Category, j.Title, j.Duration, j.Salary, c.Name as CompanyName 
                FROM Job_offer j 
                LEFT JOIN Company c ON j.ID_company = c.ID_company 
                WHERE 1=1"; 
        
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (j.Title LIKE :keyword OR j.Description LIKE :keyword OR c.Name LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($category)) {
            $sql .= " AND j.Category = :category";
            $params[':category'] = $category;
        }

        if (!empty($minSalary)) {
            $sql .= " AND j.Salary >= :min_salary";
            $params[':min_salary'] = (float)$minSalary;
        }

        if (!empty($duration)) {
            $sql .= " AND j.Duration = :duration";
            $params[':duration'] = (int)$duration;
        }

        $sql .= " ORDER BY j.ID_offer DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

