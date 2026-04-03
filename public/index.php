<?php

require_once '../vendor/autoload.php';
use App\Controllers\HomeController;
use App\Controllers\ConnexionController;
use App\Controllers\SearchController;
use App\Controllers\UserController;
use App\Controllers\AccountController;
use App\Controllers\ApplyFormController;
use App\Controllers\WishlistController;
use App\Controllers\OfferController;
use App\Controllers\CompanyController;
use App\Controllers\PromotionController;
use App\Controllers\EvaluationController;
use App\Controllers\ProfilController;
use App\Controllers\ManageOfferController;
use App\Core\Router;
use App\Core\Database;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/Views');
$twig   = new \Twig\Environment($loader);





$db  = new Database();
$pdo = $db->connect();

$ConnexionController = new ConnexionController($twig,$pdo);

$router = new Router($_GET['url'] ?? '');

$router->get('/', function () use ($twig, $pdo) {
    $controller = new HomeController($twig, $pdo);
    $controller->index();
});
$router->get('/admin/Promotions/:id', function ($id) use ($twig, $pdo) {
    $controller = new PromotionController($twig, $pdo);
    $controller->index($id);
});
$router->get('/offer', function () use ($twig, $pdo) {
    $controller = new OfferController($twig, $pdo);
    $controller->index();
});
// FIX : méthode renommée printConnexion() — gère GET et POST en interne
$router->get('/connexion', function () use ($ConnexionController) {
    $ConnexionController->printConnexion();
});

$router->post('/connexion', function () use ($ConnexionController) {
    $ConnexionController->printConnexion();
});

$router->get('/deconnexion', function () use ($ConnexionController) {
    $ConnexionController->deconnect();
    header('Location: /');
    exit;
});
$router->get('/postuler/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $ConnexionController->needConnexion();
    $controller = new ApplyFormController($twig, $pdo);

    $controller->printApplyForm($id);
});
$router->post('/postuler/:id', function ($id) use ($twig,$pdo, $ConnexionController) {
    $ConnexionController->needConnexion();
    $controller = new ApplyFormController($twig, $pdo);
    $controller->storeCandidacy($id);
});
$router->get('/wishlist', function () use ($twig, $pdo) {
    $controller = new WishlistController($twig, $pdo);
    $controller->index();
});
$router->get('/wishlist/add/:id', function ($id) use ($twig, $pdo) {
    $controller = new WishlistController($twig, $pdo);
    $controller->add($id);
});
$router->get('/wishlist/delete/:id', function ($id) use ($twig, $pdo) {
    $controller = new WishlistController($twig, $pdo);
    $controller->delete($id);
});

$router->get('/espace-compte', function () use ($twig, $pdo) {
    $controller = new AccountController($twig, $pdo);
    $controller->index();
});

$router->get('/admin/utilisateurs', function () use ($twig,$pdo,$ConnexionController){
    $ConnexionController->needAdmin();
    $controller=new SearchController($twig,$pdo);
    $controller->searchUser();
});
$router->post('/admin/utilisateurs/results', function () use ($twig,$pdo){
    $controller=new SearchController($twig,$pdo);
    $controller->resultUser();
});
$router->get('/admin/utilisateurs/results', function () use ($twig,$pdo){
    $controller=new SearchController($twig,$pdo);
    $controller->resultUser();
});
$router->post('/admin/utilisateurs/delete/:id', function ($id) use ($twig,$pdo,$ConnexionController) {
    $ConnexionController->needAdmin();
    $controller=new UserController($twig,$pdo);
    $controller->userDelete($id);
});
$router->post('/admin/utlisateur/modify/:id', function ($id) use ($twig,$pdo,$ConnexionController) {
    $ConnexionController->needAdmin();
    $controller=new UserController($twig,$pdo);
    $controller->userModify($id);
});
$router->post('/admin/utlisateur/modified/:id', function ($id) use ($twig,$pdo,$ConnexionController) {
    $ConnexionController->needAdmin();
    $controller=new UserController($twig,$pdo);
    $controller->userModified($id);
});
$router->get('/admin/utilisateurs/create', function () use ($twig,$pdo,$ConnexionController) {
    $ConnexionController->needAdmin();
    $controller=new UserController($twig,$pdo);
    $controller->createMenu();
});
$router->post('/inscription', function () use ($twig,$pdo,$ConnexionController) {
    $ConnexionController->needAdmin();
    $controller=new UserController($twig,$pdo);
    $controller->userCreate();
});
$router->get('/utilisateur/:id', function ($id) use ($twig,$pdo,$ConnexionController) {
    $ConnexionController->needAdmin();
    $controller=new SearchController($twig,$pdo);
    $controller->showUser($id);
});

$router->get('/admin/entreprises', function () use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->index();
});

$router->get('/admin/entreprises/create', function () use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->create();
});

$router->post('/admin/entreprises/store', function () use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->store();
});

$router->get('/admin/entreprises/list', function () use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->list();
});

$router->get('/admin/entreprises/delete/:id', function ($id) use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->delete($id);
});

$router->get('/admin/entreprises/edit/:id', function ($id) use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->edit($id);
});

$router->post('/admin/entreprises/update/:id', function ($id) use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->update($id);
});

// Mentions légales
$router->get('/mentions', function () use ($twig) {
    echo $twig->render('mentions-legales.html.twig'); 
});

$router->get('/evaluate/:id', function ($id) use ($twig, $pdo) {
    $controller = new EvaluationController($twig, $pdo);
    $controller->create($id);
});

$router->post('/evaluate/:id', function ($id) use ($twig, $pdo) {
    $controller = new EvaluationController($twig, $pdo);
    $controller->store($id);
});

// Voir les évaluations d'une entreprise (Admin)
$router->get('/admin/entreprises/evaluations/:id', function ($id) use ($twig, $pdo) {
    $controller = new CompanyController($twig, $pdo);
    $controller->showEvaluations($id);
});

// Infos profil
$router->get('/profil/infos', function () use ($twig, $pdo) {
    $controller = new ProfilController($twig, $pdo);
    $controller->showInfos();
});

$router->get('/admin/offer', function () use ($twig, $pdo, $ConnexionController) {
    $controller = new ManageOfferController($twig, $pdo);
    $controller->index();
});

$router->get('/admin/offer/create', function () use ($twig, $pdo, $ConnexionController) {
    $controller = new ManageOfferController($twig, $pdo);
    $controller->form();
});


$router->get('/admin/offer/edit/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $controller = new ManageOfferController($twig, $pdo);
    $controller->form($id);
});


$router->post('/admin/offer/save', function () use ($twig, $pdo, $ConnexionController) {
    $controller = new ManageOfferController($twig, $pdo);
    $controller->save();
});

$router->get('/admin/offer/delete/:id', function ($id) use ($twig, $pdo, $ConnexionController) {
    $controller = new ManageOfferController($twig, $pdo);
    $controller->delete($id);
});

$router->run();
