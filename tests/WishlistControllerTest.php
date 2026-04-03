<?php

namespace Tests\Controllers;

use App\Controllers\WishlistController;
use App\Models\WishlistModel;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour WishlistController.
 *
 * Un contrôleur dépend de plusieurs objets externes :
 *   - $twig  : le moteur de templates qui génère le HTML
 *   - $pdo   : la connexion à la base de données
 *   - $Model : le modèle qui contient la logique BDD (WishlistModel)
 *
 * On va mocker Twig et WishlistModel pour ne tester que la logique
 * du contrôleur, sans rendu HTML réel ni requête SQL.
 *
 * Problème particulier : add() et delete() appellent header() et exit()
 * qui sont des fonctions PHP natives impossibles à mocker directement.
 * Pour contourner ça, on utilise une classe enfant qui redéfinit ces appels.
 */

/**
 * Classe enfant de WishlistController qui neutralise header() et exit().
 * Sans ça, appeler add() ou delete() dans un test ferait planter PHPUnit
 * car exit() stoppe complètement l'exécution du script.
 */
class TestableWishlistController extends WishlistController
{
    // Stocke les redirections déclenchées par header('Location: ...')
    public array $redirections = [];

    // Indique si exit() a été "appelé" (sans vraiment stopper le script)
    public bool $exited = false;

    /**
     * On redéfinit redirect() pour capturer la redirection
     * au lieu d'exécuter un vrai header() PHP.
     */
    protected function redirect(string $url): void
    {
        $this->redirections[] = $url;
        $this->exited = true;
    }

    /**
     * Permet d'injecter un mock du modèle depuis le test.
     * Nécessaire car $Model est protected dans la classe parente Controller,
     * donc inaccessible directement depuis l'extérieur de la classe.
     */
    public function setModel(object $model): void
    {
        $this->Model = $model;
    }
}

class WishlistControllerTest extends TestCase
{
    // Le contrôleur qu'on va tester (version testable sans header/exit)
    private TestableWishlistController $controller;

    // Faux objet Twig pour éviter tout rendu HTML réel
    private object $twigMock;

    // Faux objet WishlistModel pour éviter toute requête SQL réelle
    private WishlistModel $modelMock;

    protected function setUp(): void
    {
        // --- MOCK DE TWIG ---
        // Twig est une classe tierce qui génère du HTML.
        // On crée un faux Twig qui ne fait rien, juste pour que le
        // contrôleur puisse appeler render() sans erreur.
        $this->twigMock = $this->getMockBuilder(\Twig\Environment::class)
                               ->disableOriginalConstructor() // pas besoin d'initialiser Twig pour de vrai
                               ->getMock();

        // --- MOCK DU MODÈLE ---
        // On crée un faux WishlistModel pour simuler les interactions BDD.
        // On passe null comme PDO car le mock n'exécutera aucune vraie requête.
        $pdoStub = $this->createMock(\PDO::class);
        $this->modelMock = $this->getMockBuilder(WishlistModel::class)
                                ->setConstructorArgs([$pdoStub])
                                ->getMock();

        // --- INSTANCIATION DU CONTRÔLEUR ---
        // On crée le contrôleur avec les faux objets.
        // Ensuite on remplace manuellement son Model par notre mock
        // pour éviter que le vrai WishlistModel soit instancié dans le constructeur.
        $this->controller = new TestableWishlistController($this->twigMock, $pdoStub);
        // On utilise setModel() car $Model est protected dans Controller,
        // donc inaccessible directement avec $this->controller->Model = ...
        $this->controller->setModel($this->modelMock);
    }

    // =========================================================================
    // TEST 1 : index()
    // =========================================================================

    /**
     * Cas testé : index() récupère les offres de l'utilisateur
     * et les passe à Twig pour le rendu du template wishlist.html.twig.
     */
    public function testIndex(): void
    {
        // --- DONNÉES FICTIVES ---
        // On simule deux offres retournées par le modèle pour l'utilisateur ID = 1
        $fakeOffres = [
            ['ID_offer' => 10, 'Title' => 'Développeur PHP'],
            ['ID_offer' => 11, 'Title' => 'Alternance DevOps'],
        ];

        // --- CONFIGURATION DU MOCK MODÈLE ---
        // On dit au faux modèle : quand getAllByUserId() est appelé,
        // retourne nos fausses offres. On vérifie aussi qu'il est bien appelé une fois.
        $this->modelMock->expects($this->once())
                        ->method('getAllByUserId')
                        ->with(1) // l'ID utilisateur codé en dur dans le contrôleur
                        ->willReturn($fakeOffres);

        // --- CONFIGURATION DU MOCK TWIG ---
        // On dit au faux Twig : vérifie que render() est appelé avec le bon template
        // et les bonnes variables (offres + page_title).
        $this->twigMock->expects($this->once())
                       ->method('render')
                       ->with(
                           'wishlist.html.twig', // nom du template attendu
                           [
                               'offres'      => $fakeOffres,
                               'page_title'  => 'Ma Wish-list'
                           ]
                       )
                       ->willReturn('<html>fake</html>'); // retourne du HTML fictif

        // --- APPEL DE LA MÉTHODE ---
        $this->controller->index();
    }

    // =========================================================================
    // TEST 2 : add()
    // =========================================================================

    /**
     * Cas testé : add() appelle bien Model->add() avec les bons IDs
     * quand un ID d'offre valide est passé en paramètre.
     */
    public function testAdd(): void
    {
        // --- CONFIGURATION DU MOCK MODÈLE ---
        // On vérifie que add() est appelé une fois avec l'ID user = 1 et l'ID offre = 5
        $this->modelMock->expects($this->once())
                        ->method('add')
                        ->with(
                            1, // ID utilisateur codé en dur dans le contrôleur
                            5  // ID de l'offre qu'on passe en paramètre
                        );

        // --- APPEL DE LA MÉTHODE ---
        $this->controller->add(5);

        // --- ASSERTION ---
        // Après l'ajout, le contrôleur doit rediriger vers /wishlist
        $this->assertTrue($this->controller->exited);
        $this->assertContains('/wishlist', $this->controller->redirections);
    }

    // =========================================================================
    // TEST 3 : delete()
    // =========================================================================

    /**
     * Cas testé : delete() appelle bien Model->remove() avec les bons IDs
     * quand un ID d'offre valide est passé en paramètre.
     */
    public function testDelete(): void
    {
        // --- CONFIGURATION DU MOCK MODÈLE ---
        // On vérifie que remove() est appelé une fois avec l'ID user = 1 et l'ID offre = 8
        $this->modelMock->expects($this->once())
                        ->method('remove')
                        ->with(
                            1, // ID utilisateur codé en dur dans le contrôleur
                            8  // ID de l'offre qu'on passe en paramètre
                        );

        // --- APPEL DE LA MÉTHODE ---
        $this->controller->delete(8);

        // --- ASSERTION ---
        // Après la suppression, le contrôleur doit rediriger vers /wishlist
        $this->assertTrue($this->controller->exited);
        $this->assertContains('/wishlist', $this->controller->redirections);
    }
}
