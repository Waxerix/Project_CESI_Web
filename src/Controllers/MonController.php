<?php


namespace App\Controllers;

use App\Models\TaskModel;

class MonController extends Controller
{
    public function __construct($templateEngine)
    {
        $this->model = new TaskModel();
        $this->templateEngine = $templateEngine;
    }
    public function welcomePage()
    {


        echo $this->templateEngine->render('index.html.twig', [
            'roles' => $this->model->getSelectForRole(),
        ]);
    }
    public function newUser()
    {
        $this->model->newUser();
        header('Location: /?uri=/');
        exit();
    }

    public function pageInscription()
    {

        
        echo $this->templateEngine->render('inscription.html.twig', [
            'roles' => $this->connection->getAllRole(),
        ]);
    }
    public function loginPage()
    {


        echo $this->templateEngine->render('connexion.html.twig', [
            'roles' => $this->model->getSelectForRole(),
        ]);
    }
    
}