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
        $allCategory = array_column($offres, 'Category');
        $UniqueCategory = array_unique($allCategory);
        $flash = null;
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        
        echo $this->twig->render('index.html.twig', [
            'offres_emploi' => $offres,
            'category' => $UniqueCategory,
            'user' => $user,
            'flash' => $flash,
        ]);
    }
}