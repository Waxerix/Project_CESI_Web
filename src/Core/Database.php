<?php
namespace App\core;
use PDO;
use PDOException;
/**
 * This interface represents a database.
 */
class Database
{

    public function __construct()
    {
        // Ici, on pourrait initialiser une connexion à une base de données réelle
    }
    public function connect()
    {
        try {
            $pdo = new PDO('mysql:host=db;dbname=projet_web;charset=utf8', 'dev', 'dev');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
        return $pdo;
    }
}