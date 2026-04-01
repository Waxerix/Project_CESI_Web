<?php
namespace App\Models;
use App\Core\Model;

class CandidacyModel extends Model
{
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function storeFile($file): ?string
    {
        $targetDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return '/uploads/' . $filename;
        }
        return null;
    }
    public function createCandidacy(int $userId, int $offerId, string $comment): bool
    {
        $verif = $this->pdo->prepare(
            'SELECT COUNT(*) FROM Apply WHERE ID_user = :userId AND ID_offer = :offerId'
        );
        $verif->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $verif->bindValue(':offerId', $offerId, \PDO::PARAM_INT);
        $verif->execute();
        $count = $verif->fetchColumn();

        if ($count > 0) {
            return false; // L'utilisateur a déjà postulé à cette offre
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO Apply (ID_user, ID_offer, Comment)
             VALUES (:userId, :offerId, :comment)'
        );
        $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':offerId', $offerId, \PDO::PARAM_INT);
        $stmt->bindValue(':comment', $comment, \PDO::PARAM_STR);
        return $stmt->execute();
    }
    

}