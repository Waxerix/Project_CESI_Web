<?php

require_once '../vendor/autoload.php';
require_once '../src/Controllers/HomeController.php';
// Ajout de l'import du nouveau contrôleur
require_once '../src/Controllers/WishlistController.php'; 

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/View');
$twig = new \Twig\Environment($loader);

$twig->addFunction(new \Twig\TwigFunction('path', function ($nomDeLaRoute, $parametres = []) {
    $routes = [
        'app_home'         => 'index.php',
        'app_inscription'  => 'index.php?page=inscription',
        'app_mentions'     => 'index.php?page=mentions',
        'app_connexion'    => 'index.php?page=connexion',
        'app_postuler'     => 'index.php?page=postuler',
        // --- AJOUTS WISHLIST ---
        'app_wishlist'     => 'index.php?page=wishlist',
        'app_wishlist_add' => 'index.php?page=wishlist_add',
        'app_wishlist_del' => 'index.php?page=wishlist_del',
    ];

    $url = $routes[$nomDeLaRoute] ?? 'index.php';
    if (!empty($parametres) && isset($parametres['id'])) {
        $url .= '&id=' . $parametres['id'];
    }
    return $url;
}));

try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet_web;charset=utf8', 'phpmyadmin', 'Nams25050614!!');
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

    // --- NOUVEAUX CASES POUR LA WISHLIST ---
    case 'wishlist':
        $controller = new \App\Controllers\WishlistController($twig, $pdo);
        $controller->index();
        break;

    case 'wishlist_add':
        $id = $_GET['id'] ?? null;
        $controller = new \App\Controllers\WishlistController($twig, $pdo);
        $controller->add($id);
        break;

    case 'wishlist_del':
        $id = $_GET['id'] ?? null;
        $controller = new \App\Controllers\WishlistController($twig, $pdo);
        $controller->delete($id);
        break;

    case 'home':
    default:
        $controller = new HomeController($twig, $pdo);
        $controller->index();
        break;
}