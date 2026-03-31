<?php

require_once '../vendor/autoload.php';
require_once '../src/Controllers/HomeController.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/View');
$twig = new \Twig\Environment($loader);

$twig->addFunction(new \Twig\TwigFunction('path', function ($nomDeLaRoute, $parametres = []) {
    $routes = [
        'app_home'        => 'index.php',
        'app_inscription' => 'index.php?page=inscription',
        'app_mentions'    => 'index.php?page=mentions',
        'app_connexion'   => 'index.php?page=connexion',
        'app_postuler'    => 'index.php?page=postuler',
    ];

    $url = $routes[$nomDeLaRoute] ?? 'index.php';
    if (!empty($parametres) && isset($parametres['id'])) {
        $url .= '&id=' . $parametres['id'];
    }
    return $url;
}));


try {
    $pdo = new PDO('mysql:host=db;dbname=projet_web;charset=utf8', 'dev', 'dev');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}


$pageDemandee = $_GET['page'] ?? 'home'; 

switch ($pageDemandee) {
    
    case 'inscription':
        echo $twig->render('inscription.html.twig', []);
        break;

    case 'mentions':
        echo $twig->render('mentions-legales.html.twig', []);
        break;

    case 'connexion':
        echo $twig->render('connexion.html.twig', []);
        break;

    case 'postuler':
        echo $twig->render('formulaire-postuler.html.twig', []);
        break;

    case 'home':
    default:
        $controller = new \App\Controllers\UserController($twig, $pdo);
        $controller->userSearch();
        break;
}