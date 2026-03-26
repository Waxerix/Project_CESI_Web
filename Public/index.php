<?php
/**
 * This is the router, the main entry point of the application.
 * It handles the routing and dispatches requests to the appropriate controller methods.
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../vendor/autoload.php";

use App\Controllers\MonController;

$loader = new \Twig\Loader\FilesystemLoader('../src/View');
$twig = new \Twig\Environment($loader, [
    'debug' => true
]);

if (isset($_GET['uri'])) {
    $uri = $_GET['uri'];
} else {
    $uri = '/';
}
$controller = new MonController($twig);

switch ($uri) {
    case '/':
        
        // TODO : call the welcomePage method of the controller
        $controller->welcomePage();
        
        break;
    case 'newUser':
        // TODO : call the newUser method of the controller
        $controller->newUser();
        break;
    case 'inscription':
        // TODO : call the checkTask method of the controller
        $controller->pageInscription();
        break;
    case 'login':
        // TODO : call the historyPage method of the controller
        $controller->loginPage();
        break;
    case 'uncheck_task':
        // TODO : call the uncheckTask method of the controller
        echo 'Uncheck task action';
        break;
    case 'about':
        // TODO : call the aboutPage method of the controller
        echo 'About page';
        break;
    default:
        // TODO : return a 404 error
        echo '404 Not Found';
        break;
}