<?php
// index.php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Controllers/HomeController.php';

// --- 1. INITIALISATION DE TWIG ---
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
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
    $pdo = new PDO('mysql:host=localhost;dbname=sesomate;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// --- 3. LE ROUTEUR (Distribution du trafic) ---
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

    case 'home':
    default:
        // On inclut et on appelle le Contrôleur
        require_once __DIR__ . '/src/Controllers/HomeController.php';
        $controller = new HomeController($twig, $pdo);
        $controller->index();
        break;
}