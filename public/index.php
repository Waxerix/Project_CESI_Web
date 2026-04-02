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
$router->post('/admin/utilisateurs/delete/:id', function ($id) use ($twig,$pdo,$ConnexionController) {
    $ConnexionController->needAdmin();
    $controller=new UserController($twig,$pdo);
    $controller->userDelete($id);
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

// 1. 기업 관리 메인 메뉴 (등록/리스트 선택 화면)
$router->get('/admin/entreprises', function () use ($twig, $pdo) {
    $controller = new \App\Controllers\CompanyController($twig, $pdo);
    $controller->index();
});

// 2. 기업 등록 페이지 표시 (폼 화면)
$router->get('/admin/entreprises/create', function () use ($twig, $pdo) {
    $controller = new \App\Controllers\CompanyController($twig, $pdo);
    $controller->create();
});

// 3. 기업 데이터 저장 처리 (POST)
$router->post('/admin/entreprises/store', function () use ($twig, $pdo) {
    $controller = new \App\Controllers\CompanyController($twig, $pdo);
    $controller->store();
});

// 4. 기업 리스트 페이지 표시 (테이블 화면)
$router->get('/admin/entreprises/list', function () use ($twig, $pdo) {
    $controller = new \App\Controllers\CompanyController($twig, $pdo);
    $controller->list();
});

// 5. 기업 삭제 처리 (ID 파라미터 수신)
$router->get('/admin/entreprises/delete/:id', function ($id) use ($twig, $pdo) {
    $controller = new \App\Controllers\CompanyController($twig, $pdo);
    $controller->delete($id);
});

$router->run();
