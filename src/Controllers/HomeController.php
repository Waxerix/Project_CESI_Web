<?php

require_once __DIR__ . '/../Models/JobOfferModel.php';

class HomeController {
    private $twig;
    private $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    // La méthode qui gère l'affichage de la page d'accueil
    public function index() {
        // 1. On appelle le Modèle
        $jobOfferModel = new JobOfferModel($this->pdo);
        
        // 2. On récupère les données
        $offres = $jobOfferModel->getAllOffers();

        // 3. On envoie tout à la Vue (Twig)
        echo $this->twig->render('index.html.twig', [
            'offres_emploi' => $offres
        ]);
    }
}