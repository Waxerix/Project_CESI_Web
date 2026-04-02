<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\PiloteModel;

class UserController extends Controller{
    private $UserModel;
    private $StudentModel;
    private $PiloteModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->UserModel = new UserModel($this->pdo);
        $this->StudentModel = new StudentModel($this->pdo);
        $this->PiloteModel = new PiloteModel($this->pdo);
    }

    public function createMenu(){
        echo $this->twig->render("inscription.html.twig",[
            'roles' => $this->UserModel->rolesList()
        ]);
}


    public function userCreate() {
        // Imaginons que les données viennent d'un formulaire POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['Role']=='Etudiant'){
                try {
                    $this->StudentModel->createStudent(
                        $_POST['email'],
                        $_POST['password'],
                        $_POST['firstname'],
                        $_POST['lastname'],
                        $_POST['phone']
                    );
                    header('Location: /admin/utilisateurs');
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
            if ($_POST['Role']=='Pilote'){
                try {
                    $this->PiloteModel->createPilote(
                        $_POST['email'],
                        $_POST['password'],
                        $_POST['firstname'],
                        $_POST['lastname'],
                        $_POST['phone']
                    );
                    header('Location: /admin/utilisateurs');
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
        }
        header('Location: /admin/utilisateurs');
        exit;
    }

    public function userDelete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->StudentModel->isStudent($id)){
                $this->StudentModel->deleteStudent($id);
            }
        } 
        if ($this->PiloteModel->isPilote($id)){
            $this->PiloteModel->deletePilote($id);
        }
        header('Location: /admin/utilisateurs');
        exit;
    }


}