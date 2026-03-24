<?php

require_once __DIR__ . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

$twig->addFunction(new \Twig\TwigFunction('asset', function ($cheminFichier) {
    $base = '/Project_CESI_Web/'; 
    return $base . ltrim($cheminFichier, '/');
}));


$twig->addFunction(new \Twig\TwigFunction('path', function ($nomDeLaRoute) {
  
    $routes = [
        'app_home'        => 'index.php',
        'app_inscription' => 'index.php?page=inscription',
        'app_mentions'    => 'index.php?page=mentions',
        'app_connexion'   => 'index.php?page=connexion',
       
        ];

    
    return $routes[$nomDeLaRoute] ?? 'index.php';
}));


$pageDemandee = $_GET['page'] ?? 'home'; 

switch ($pageDemandee) {
    
    case 'inscription':
        
        echo $twig->render('inscription.html.twig', [
        ]);
        break;

    case 'mentions':
        
        echo $twig->render('mentions-legales.html.twig', [
        ]);
        break;

     case 'connexion':
        
        echo $twig->render('connexion.html.twig', [
        ]);
        break;

    case 'home':
    default:
        
        echo $twig->render('index.html.twig', [
        ]);
        break;

    
}