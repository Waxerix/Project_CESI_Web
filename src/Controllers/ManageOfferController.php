<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\JobOfferModel;
use App\Models\AccessModel;

class ManageOfferController extends Controller {
    private $OfferModel;
    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->OfferModel = new JobOfferModel($pdo);
        $this->Model = new AccessModel($pdo);
    }

    // Affiche le tableau avec toutes les offres
    public function index() {
        $user = $this->Model->currentUser();
        $offres = $this->OfferModel->getAllOffers(); // Utilisez votre méthode existante qui liste tout

        echo $this->twig->render('offers_list.html.twig', [
            'user'=> $user,
            'offres' => $offres
        ]);
    }

    // Affiche le formulaire (vide pour une création, pré-rempli pour une modification)
    public function form($id_offer = null) {
        $offer = null;
        $user = $this->Model->currentUser();
        // Si un ID est fourni, c'est une modification, on récupère les données
        if ($id_offer) {
            $model = new JobOfferModel($this->pdo);
            $offer = $model->getOfferById($id_offer);
        }

        // Il faudrait aussi récupérer la liste des entreprises pour le menu déroulant (<select>)
        $stmt = $this->pdo->query("SELECT * FROM Company");
        $companies = $stmt->fetchAll();

        echo $this->twig->render('offer_form.html.twig', [
            'offer' => $offer,
            'user' => $user,
            'companies' => $companies
        ]);
    }

    // Traite les données du formulaire quand on clique sur "Enregistrer"
    public function save() {
        
        $data = [
            'Title' => $_POST['Title'],
            'Description' => $_POST['Description'],
            'Category' => $_POST['Category'],
            'Salary' => $_POST['Salary'],
            'Duration' => $_POST['Duration'],
            'Start_date' => $_POST['Start_date'],
            'ID_company' => $_POST['ID_company']
        ];

        if (!empty($_POST['ID_offer'])) {
            // Modification
            $this->OfferModel->updateOffer($_POST['ID_offer'], $data);
        } else {
            // Création
            $data['Create_date'] = date('Y-m-d'); // Date du jour
            $this->OfferModel->createOffer($data);
        }

        header('Location: /admin/offer');
        exit();
    }

    // Supprime l'offre
    public function delete($id_offer) {
        $model = new JobOfferModel($this->pdo);
        $model->deleteOffer($id_offer);
        
        header('Location: /admin/offer');
        exit();
    }
}