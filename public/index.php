<?php

require_once '../vendor/autoload.php';
require_once '../src/Controllers/HomeController.php';
use App\Controllers\ConnexionController;
use App\Controllers\SearchController;
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
$router->get('/postuler/:id', function ($id) use ($twig) {
    $twig->render('formulaire-postuler.html.twig', ['id' => $id]);
});
$router->post('/inscription', function () use ($twig,$pdo){
    $controller=new InscriptionController($twig,$pdo);
});
$router->post('/search', function () use ($twig,$pdo){
    $controller=new SearchController($twig,$pdo);
    $controller->search();
});
$router->get('/utilisateur/:id', function ($id) use ($twig,$pdo) {
    $controller=new SearchController($twig,$pdo);
    $controller->showUser($id);
});
$router->run();
