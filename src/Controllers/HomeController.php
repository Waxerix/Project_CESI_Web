<?php
namespace App\Controllers;
use App\Models\JobOfferModel;
use App\Models\AccessModel;
class HomeController
{
    private $twig;
    private $pdo;


    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;

    }

    public function index()
    {
        $access = new AccessModel($this->pdo);
        $user = $access->currentUser(); // démarre la session proprement
        
        $jobOfferModel = new JobOfferModel($this->pdo);

        $offres = $jobOfferModel->getBestOffer();

        echo $this->twig->render('index.html.twig', [
            'offres_emploi' => $offres,
            'user' => $user['pseudo'],
            'user_admin' => $user['admin'] ?? false,
        ]);
    }

}