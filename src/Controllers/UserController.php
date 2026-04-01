<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\StudentModel;
use App\Models\PiloteModel;

class UserController extends Controller{
    private $StudentModel;
    private $PiloteModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->StudentModel = new StudentModel($this->pdo);
        $this->PiloteModel = new PiloteModel($this->pdo);
    }

    public function deleteUser($id) {
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

    public function result() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['filter']=== 'Student'){
                try {
                    $users = $this->StudentModel->searchStudent($_POST['search']);

                    echo $this->twig->render('user-search.html.twig', [
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

                    echo $this->twig->render('user-search.html.twig', [
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

    public function showUser($id){
        $sql = "
            SELECT * FROM User_ u 
            INNER JOIN Profil p ON u.ID_profil = p.ID_profil
            INNER JOIN Role r ON u.ID_role = r.ID_role
            WHERE ID_user= :id ;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        echo $this->twig->render('user-informations.html.twig', [
            'user' => $user
        ]);


    }
}