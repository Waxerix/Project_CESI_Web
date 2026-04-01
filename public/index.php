<?php

require_once '../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\ConnexionController;
use App\Controllers\ApplyFormController;
use App\Controllers\WishlistController;
use App\Controllers\CompanyController; // Ajout du contrôleur d'entreprise
use App\Core\Router;
use App\Core\Database;

// Initialisation de Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/Views');
$twig   = new \Twig\Environment($loader);

// Connexion à la base de données
$db  = new Database();
$pdo = $db->connect();

// Initialisation du Routeur
$router = new Router($_GET['url'] ?? '');

// --- ROUTES PRINCIPALES ---

// Page d'accueil
$router->get('/', function () use ($twig, $pdo) {
    $controller = new HomeController($twig, $pdo);
    $controller->index();
});

// --- AUTHENTIFICATION ---

// Connexion (Affichage et Traitement)
$router->get('/connexion', function () use ($twig, $pdo) {
    $controller = new ConnexionController($twig, $pdo);
    $controller->printConnexion();
});

$router->post('/connexion', function () use ($twig, $pdo) {
    $controller = new ConnexionController($twig, $pdo);
    $controller->printConnexion();
});

// Déconnexion
$router->get('/deconnexion', function () use ($twig, $pdo) {
    $controller = new ConnexionController($twig, $pdo);
    $controller->deconnect();
    header('Location: /');
    exit;
});

// --- CANDIDATURES ---

// Formulaire de candidature
$router->get('/postuler/:id', function ($id) use ($twig, $pdo) {
    $controller = new ApplyFormController($twig, $pdo);
    $controller->printApplyForm($id);
});

// Enregistrement de la candidature
$router->post('/postuler/:id', function ($id) use ($twig, $pdo) {
    $controller = new ApplyFormController($twig, $pdo);
    $controller->storeCandidacy($id);
});

// --- WISHLIST (FAVORIS) ---

// Page de la liste de souhaits
$router->get('/wishlist', function () use ($twig, $pdo) {
    $controller = new WishlistController($twig, $pdo);
    $controller->index();
});

// Ajout aux favoris (Changé en GET pour correspondre au JS fetch)
$router->get('/wishlist/add/:id', function ($id) use ($twig, $pdo) {
    $controller = new WishlistController($twig, $pdo);
    $controller->add($id);
});

// Suppression des favoris (Changé en GET pour correspondre au JS fetch)
$router->get('/wishlist/delete/:id', function ($id) use ($twig, $pdo) {
    $controller = new WishlistController($twig, $pdo);
    $controller->delete($id);
});

// --- GESTION DES ENTREPRISES ---

// Affichage du formulaire de création d'entreprise
$router->get('/company/create', function () use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->index();
});

// Traitement de la création d'entreprise
$router->post('/company/store', function () use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->store();
});

// Lancement du routeur
$router->run();