<?php

namespace App\Controllers;

use App\Models\StudentModel;

class UserController {
    private $twig;
    private $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function userSearch() {
        $StudentModel = new StudentModel($this->pdo);
        
        $users = $StudentModel->searchStudent("an");

        echo $this->twig->render('user-gestion.html.twig', [
            'users' => $users
        ]);
    }
}