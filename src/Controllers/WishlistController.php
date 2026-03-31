<?php
namespace App\Controllers;

use App\Models\WishlistModel;

class WishlistController extends Controller {

    public function __construct($templateEngine, $pdo) {
        // On utilise les propriétés héritées de Controller.php
        $this->templateEngine = $templateEngine;
        $this->pdo = $pdo;
        // On initialise le modèle
        $this->model = new WishlistModel($this->pdo);
    }

    public function index() {
        $id_user = 1; 
        // Vérifie bien que la méthode s'appelle getAllByUserId dans le modèle
        $offres = $this->model->getAllByUserId($id_user);

        echo $this->templateEngine->render('wishlist.html.twig', [
            'offres' => $offres,
            'page_title' => 'Ma Wish-list'
        ]);
    }

    public function add($id_offer) {
        $id_user = 1; 
        if ($id_offer) {
            $this->model->add($id_user, $id_offer);
        }
        header('Location: index.php?page=wishlist');
        exit();
    }

    public function delete($id_offer) {
        $id_user = 1; 
        if ($id_offer) {
            $this->model->remove($id_user, $id_offer);
        }
        header('Location: index.php?page=wishlist');
        exit();
    }
}