<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\PiloteModel;
use App\Models\AccessModel;

class SearchController extends Controller{
    private $StudentModel;
    private $PiloteModel;
    private $UserModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->UserModel = new UserModel($this->pdo);
        $this->StudentModel = new StudentModel($this->pdo);
        $this->PiloteModel = new PiloteModel($this->pdo);
        $this->Model = new AccessModel($this->pdo);
    }

    public function searchUser() {
        $Access = $this->Model->currentUser();
        $users = $this->StudentModel->searchStudent('')+$this->PiloteModel->searchPilote('');
        echo $this->twig->render('user-search.html.twig', [
                        'users' => $users,
                        'roles' => $this->UserModel->rolesList(),
                        'user' => $Access,
                        'search'=> ''
                    ]);
    }

    public function resultUser() {
    $Access = $this->Model->currentUser();

    // On lit search et filter depuis POST (formulaire) ou GET (pagination)
    $search = $_POST['search'] ?? $_GET['search'] ?? '';
    $filter = $_POST['filter'] ?? $_GET['filter'] ?? '';

    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    try {
        if ($filter === 'Etudiant') {
            $users = $this->StudentModel->searchStudent($search);
        } else if ($filter === 'Pilote') {
            $users = $this->PiloteModel->searchPilote($search);
        } else {
            $users = [];
        }

        echo $this->twig->render('user-search.html.twig', [
            'users'  => $users,
            'user'   => $Access,
            'roles'  => $this->UserModel->rolesList(),
            'search' => $search,
            'filter' => $filter,
            'current_page' => $currentPage 
        ]);

    } catch (Exception $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}

    public function showUser($id){
        $Access = $this->Model->currentUser();
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
            'user' => $Access,
            'users' => $user
        ]);


    }
}