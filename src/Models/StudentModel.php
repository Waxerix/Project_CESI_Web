<?php
namespace App\Models;

class StudentModel extends Model{
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function searchStudent(string $search){
        $search = trim($search);

        if (empty($search)) {
            return [];
        }

        $searchParam = '%' . $search . '%';

        $sql = "
            SELECT
                u.ID_user,
                u.Email,
                u.status,
                p.Name,
                p.Lastname,
                p.Phone_number
            FROM User_ u
            INNER JOIN Profil p ON u.ID_profil = p.ID_profil
            WHERE
                p.Name       LIKE :search1
                OR p.Lastname LIKE :search2
                OR u.Email    LIKE :search3
            ORDER BY p.Lastname ASC, p.Name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':search1', $searchParam, \PDO::PARAM_STR);
        $stmt->bindParam(':search2', $searchParam, \PDO::PARAM_STR);
        $stmt->bindParam(':search3', $searchParam, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}