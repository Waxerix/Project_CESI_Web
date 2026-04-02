<?php
namespace App\Controllers;
use App\Core\Controller;    
use App\Models\WishlistModel;

class WishlistController extends Controller {

    public function __construct($twig, $pdo) {
        // On utilise les propriétés héritées de Controller.php
        $this->twig = $twig;
        $this->pdo = $pdo;
        // On initialise le modèle
        $this->Model = new WishlistModel($this->pdo);
    }

    protected function redirect(string $url): void
    {
    header('Location: ' . $url);
    exit();
    }

    public function index() {
        $id_user = 1; 
        // Vérifie bien que la méthode s'appelle getAllByUserId dans le modèle
        $offres = $this->Model->getAllByUserId($id_user);

        echo $this->twig->render('wishlist.html.twig', [
            'offres' => $offres,
            'page_title' => 'Ma Wish-list'
        ]);
    }

    public function add($id_offer) {
        $id_user = 1; 
        if ($id_offer) {
            $this->Model->add($id_user, $id_offer);
        }
        $this->redirect('/wishlist');
    }

    public function delete($id_offer) {
        $id_user = 1; 
        if ($id_offer) {
            $this->Model->remove($id_user, $id_offer);
        }
        $this->redirect('/wishlist');
    }
}