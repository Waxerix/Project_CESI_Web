<?php
namespace App\Models;

use App\Core\Model;


class UserModel extends Model{
   
    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
   
    public function rolesList(){
        $sql = "SELECT Status FROM Role";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
