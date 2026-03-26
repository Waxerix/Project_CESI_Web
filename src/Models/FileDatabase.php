<?php

namespace App\Models;

use PDO;
use PDOException;

class FileDatabase implements Database
{
    private PDO $pdo;

    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'projet_web';
        $username = 'phpmyadmin';
        $password = '_Mathis2006_';

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new FileDatabaseException(
                'Échec de la connexion : ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    public function getAllRole(): array
    {
        try {
            return $this->pdo
                ->query("SELECT * FROM Role")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new FileDatabaseException(
                'Impossible de récupérer les rôles : ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    // ✅ Corrigé : User_ au lieu de User
    public function getUserById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM User_ WHERE ID_user = :id");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            return $row !== false ? $row : null;
        } catch (PDOException $e) {
            throw new FileDatabaseException(
                'Impossible de récupérer l\'utilisateur : ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    // ✅ Colonnes alignées avec ta table : ID_user, Email, Password, ID_profil, ID_role
    public function insertUser(string $email, string $password, string $status): void
    {
        try {
            // 1 — Créer d'abord un profil vide et récupérer son ID
            $stmt = $this->pdo->prepare("
            INSERT INTO Profil (Lastname, Name, Phone_number) 
            VALUES (:nom, :prenom, :tel)
        ");
            $stmt->execute([
                ':nom' => $_POST['nom'] ?? '',
                ':prenom' => $_POST['prenom'] ?? '',
                ':tel' => $_POST['tel'] ?? null,
            ]);
            $profil_id = $this->pdo->lastInsertId();

            // 2 — Insérer l'utilisateur avec l'ID du profil
            $stmt = $this->pdo->prepare("
            INSERT INTO User_ (Email, Password, ID_profil, status) 
            VALUES (:email, :password, :profil_id, :status)
        ");
            $stmt->execute([
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':profil_id' => $profil_id,
                ':status' => $status,
                
            ]);

        } catch (PDOException $e) {
            throw new FileDatabaseException(
                'Impossible d\'insérer l\'utilisateur : ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }
}

class FileDatabaseException extends \Exception
{
}