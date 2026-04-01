<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\StudentModel;
use App\Models\PiloteModel;

class SearchController extends Controller{
    private $StudentModel;
    private $PiloteModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->StudentModel = new StudentModel($this->pdo);
        $this->PiloteModel = new PiloteModel($this->pdo);
    }

    public function userSearch() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['filter']=== 'Student'){
                try {
                    $users = $this->StudentModel->searchStudent($_POST['search']);

                    echo $this->twig->render('user-gestion.html.twig', [
                        'users' => $users,
                        'search'=> $_POST['search']
                    ]);
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }else if ($_POST['filter']==='Pilote'){
                try {
                    $users = $this->PiloteModel->searchPilote($_POST['search']);

                    echo $this->twig->render('user-gestion.html.twig', [
                        'users' => $users,
                        'search' => $_POST['search']
                    ]);
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur lors de l'inscription : " . $e->getMessage();
                    // Afficher l'erreur dans ton template Twig
                }
            }
        }
    }
}