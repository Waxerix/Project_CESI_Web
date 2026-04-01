<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\StudentModel;
use App\Models\PiloteModel;

class InscriptionController extends Controller{
    private $StudentModel;
    private $PiloteModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->StudentModel=new StudentModel($pdo);
        $this->PiloteModel=new PiloteModel($pdo);
    }

    public function userCreate() {
        // Imaginons que les données viennent d'un formulaire POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($POST['Role']=='etudiant'){
                try {
                    $this->studentModel->createStudent(
                        $_POST['email'],
                        $_POST['password'],
                        $_POST['firstname'],
                        $_POST['lastname'],
                        $_POST['phone']
                    );
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
            if ($POST['Role']=='pilote'){
                try {
                    $this->PiloteModel->createPilote(
                        $_POST['email'],
                        $_POST['password'],
                        $_POST['firstname'],
                        $_POST['lastname'],
                        $_POST['phone']
                    );
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
        }
    }
}