<?php
namespace App\Controllers;
use App\Models\JobOfferModel;
use App\Models\AccessModel;
class HomeController
{
    private $twig;
    private $pdo;
    

    public function __construct($twig, $pdo, $connection = null)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;

    }

    public function index()
{
    $access = new AccessModel($this->pdo);
    $user = $access->curentUser(); 

    $jobOfferModel = new JobOfferModel($this->pdo);
    $offres = $jobOfferModel->getBestOffer();

    $allCategory = array_column($offres, 'Category');
    $UniqueCategory = array_unique($allCategory);

    echo $this->twig->render('index.html.twig', [
        'offres_emploi' => $offres,
        'category' => $UniqueCategory,
        'user' => $user, 
    ]);
}

}