<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProfilModel;
use App\Models\AccessModel;

class ProfilController extends Controller {
    protected $twig;
    protected $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->Model = new AccessModel($pdo);

    }

    public function showInfos() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: /connexion'); exit(); }

        $model = new ProfilModel($this->pdo); 
        $user = $model->getUserFullInfo($_SESSION['user_id']);
        $Access = $this->Model->currentUser();
        echo $this->twig->render('profil-infos.html.twig', [
            'users' => $user,
            'user' => $Access
        ]);
    }

    public function update() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) exit();

        $model = new ProfilModel($this->pdo);
        $model->updateProfil($_SESSION['user_id'], $_POST);

        header('Location: /profil'); 
        exit();
    }
}