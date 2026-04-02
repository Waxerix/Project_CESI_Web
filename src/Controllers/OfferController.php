<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\JobOfferModel;
use App\Models\AccessModel;

class OfferController extends Controller {
    
    private $JobOfferModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->JobOfferModel = new JobOfferModel($this->pdo);
    }

    public function index() {

        $access = new AccessModel($this->pdo);
        $user = $access->currentUser();

   
        $keyword = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $minSalary = $_GET['min_salary'] ?? '';
        $duration = $_GET['duration'] ?? '';

        $offres = $this->JobOfferModel->searchAndFilterOffers($keyword, $category, $minSalary, $duration);

        echo $this->twig->render('offer.html.twig', [
            'offer' => $offres,
            'user' => $user
        ]);
    }
}