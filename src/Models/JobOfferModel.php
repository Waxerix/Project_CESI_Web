<?php


class JobOfferModel {
    private $pdo;

   
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    
    public function getAllOffers() {
        
        $sql = "SELECT j.ID_offer, j.Title, j.Duration, j.Salary, c.Name as CompanyName 
                FROM Job_offer j 
                LEFT JOIN Company c ON j.ID_company = c.ID_company 
                ORDER BY j.ID_offer DESC"; 

        $stmt = $this->pdo->query($sql);
        
       
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}