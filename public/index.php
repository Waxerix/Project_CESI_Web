<?php

require_once '../vendor/autoload.php';
require_once '../src/Controllers/HomeController.php';
require_once '../src/Controllers/AccessController.php';

use App\core\router;
use App\core\Database;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/View');
$twig   = new \Twig\Environment($loader);

$twig->addFunction(new \Twig\TwigFunction('path', function (string $nomDeLaRoute, array $parametres = []) {
    $routes = [
        'app_home'        => '/',
        'app_inscription' => '/inscription',
        'app_mentions'    => '/mentions',
        'app_connexion'   => '/connexion',
        'app_postuler'    => '/postuler',
    ];

    $url = $routes[$nomDeLaRoute] ?? '/';
    if (!empty($parametres['id'])) {
        $url .= '?id=' . urlencode($parametres['id']);
    }
    return $url;
}));

$db  = new Database();
$pdo = $db->connect();

$router = new Router($_GET['url'] ?? '');

$router->get('/', function () use ($twig, $pdo) {
    $controller = new HomeController($twig, $pdo);
    $controller->index();
});

// FIX : méthode renommée afficherConnexion() — gère GET et POST en interne
$router->get('/connexion', function () use ($twig, $pdo) {
    $controller = new AccessController($twig, $pdo);
    $controller->afficherConnexion();
});

$router->post('/connexion', function () use ($twig, $pdo) {
    $controller = new AccessController($twig, $pdo);
    $controller->afficherConnexion();
});

$router->get('/deconnexion', function () use ($twig, $pdo) {
    $controller = new AccessController($twig, $pdo);
    $controller->deconnecter();
    header('Location: /');
    exit;
});

$router->run();