<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CompanyModel;

class CompanyController extends Controller {

    private $companyModel;

    /**
     * Constructeur : Initialise Twig, PDO et le modèle Company
     */
    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        // Initialisation du modèle pour interagir avec la table Company
        $this->companyModel = new CompanyModel($this->pdo);
    }

    /**
     * Affiche le formulaire de création d'entreprise
     * URL: /company/create
     */
    public function index() {
        echo $this->twig->render('creation-entreprise.html.twig');
    }

    /**
     * Récupère les données du formulaire et les enregistre en BDD
     * URL: /company/store
     */
    public function store() {
        // On vérifie que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Récupération des données envoyées par le formulaire Twig
            // Les clés correspondent aux attributs 'name' de vos inputs HTML
            $data = [
                'name'        => $_POST['name'] ?? null,
                'email'       => $_POST['email'] ?? null,
                'phone'       => $_POST['phone'] ?? null,
                'description' => $_POST['description'] ?? null
            ];

            // Validation simple : on vérifie si les champs obligatoires sont remplis
            if ($data['name'] && $data['email']) {
                $success = $this->companyModel->create($data);

                if ($success) {
                    // Si succès, redirection vers l'accueil avec un message
                    header('Location: /?success=entreprise_creee');
                    exit();
                } else {
                    echo "Erreur lors de l'insertion dans la base de données.";
                }
            } else {
                echo "Veuillez remplir tous les champs obligatoires.";
            }
        }
    }
}