<?php

require_once __DIR__ . '/../Models/JobOfferModel.php';

class HomeController {
    private $twig;
    private $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function index() {
        $jobOfferModel = new JobOfferModel($this->pdo);
        
        $offres = $jobOfferModel->getBestOffer();

        echo $this->twig->render('index.html.twig', [
            'offres_emploi' => $offres
        ]);
    }
}