<?php
namespace App\Models;

use App\Core\Model;


class UserModel extends Model
{

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function rolesList()
    {
        $sql = "SELECT Status FROM Role";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function storePhoto($file, $userId) : ?string
    {
        $targetDir = __DIR__ . '/../../public/Photos/' . $userId .'/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile;
        }
        return NULL;
    }
}
