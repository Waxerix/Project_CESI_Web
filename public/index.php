<?php

require_once '../vendor/autoload.php';
use App\Controllers\HomeController;
use App\Controllers\ConnexionController;
use App\Controllers\ApplyFormController;
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

$router->run();
