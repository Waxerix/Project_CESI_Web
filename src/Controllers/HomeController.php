<?php

require_once __DIR__ . '/../Models/JobOfferModel.php';

class HomeController
{
    private $twig;
    private $pdo;
    private $connection;

    public function __construct($twig, $pdo, $connection = null)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;

    }

    public function index()
    {
        $access = new AccessController($this->twig, $this->pdo);
        $user = $access->utilisateurCourant(); // démarre la session proprement

        $jobOfferModel = new JobOfferModel($this->pdo);
        $offres = $jobOfferModel->getAllOffers();

        echo $this->twig->render('index.html.twig', [
            'offres_emploi' => $offres,
            'user' => $user['pseudo'],
            'user_admin' => $user['admin'],
        ]);
    }

}