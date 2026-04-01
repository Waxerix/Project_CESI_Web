<?php

require_once '../vendor/autoload.php';
use App\Controllers\HomeController;
use App\Controllers\ConnexionController;
use App\Controllers\UserSearchController;
use App\Controllers\UserController;
use App\Controllers\AccountController;
use App\Core\Router;
use App\Core\Database;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/View');
$twig   = new \Twig\Environment($loader);



$db  = new Database();
$pdo = $db->connect();

$router = new Router($_GET['url'] ?? '');

$router->get('/', function () use ($twig, $pdo) {
    $controller = new HomeController($twig, $pdo);
    $controller->index();
});

// FIX : méthode renommée printConnexion() — gère GET et POST en interne
$router->get('/connexion', function () use ($twig, $pdo) {
    $controller = new ConnexionController($twig, $pdo);
    $controller->printConnexion();
});

$router->post('/connexion', function () use ($twig, $pdo) {
    $controller = new ConnexionController($twig, $pdo);
    $controller->printConnexion();
});

$router->get('/deconnexion', function () use ($twig, $pdo) {
    $controller = new ConnexionController($twig, $pdo);
    $controller->deconnect();
    header('Location: /');
    exit;
});

$router->get('/postuler/:id', function ($id) use ($twig, $pdo) {
    $controller = new ApplyFormController($twig, $pdo);
    $controller->printApplyForm($id);
});

$router->get('/espace-compte', function () use ($twig, $pdo) {
    $controller = new AccountController($twig, $pdo);
    $controller->index();
});

$router->get('/admin/utilisateurs', function () use ($twig,$pdo){
    $controller=new UserSearchController($twig,$pdo);
    $controller->search();
});
$router->post('/admin/utilisateurs/results', function () use ($twig,$pdo){
    $controller=new UserSearchController($twig,$pdo);
    $controller->result();
});
$router->post('/admin/utilisateurs/delete/:id', function ($id) use ($twig,$pdo) {
    $controller=new UserController($twig,$pdo);
    $controller->userDelete($id);
});
$router->get('/admin/utilisateurs/create', function () use ($twig,$pdo) {
    $controller=new UserController($twig,$pdo);
    $controller->createMenu();
});
$router->post('/inscription', function () use ($twig,$pdo) {
    $controller=new UserController($twig,$pdo);
    $controller->userCreate();
});
$router->get('/utilisateur/:id', function ($id) use ($twig,$pdo) {
    $controller=new UserSearchController($twig,$pdo);
    $controller->showUser($id);
});

$router->run();
