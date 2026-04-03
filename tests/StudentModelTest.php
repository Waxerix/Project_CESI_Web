<?php

namespace Tests\Models;

use App\Models\StudentModel;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/**
 * Fichier de tests unitaires pour la classe StudentModel.
 *
 * Un test unitaire vérifie qu'une méthode fait bien ce qu'elle est censée faire,
 * sans dépendre d'une vraie base de données.
 *
 * Pour simuler la BDD, on utilise des "mocks" : ce sont de faux objets qui
 * imitent le comportement de PDO et PDOStatement sans exécuter de vraies requêtes SQL.
 * Cela permet de tester la logique du code de façon isolée et rapide.
 */
class StudentModelTest extends TestCase
{
    // L'instance de StudentModel qu'on va tester
    private StudentModel $studentModel;

    // Le faux objet PDO (mock) qui remplace la vraie connexion à la base de données
    private PDO $pdoMock;

    /**
     * setUp() est automatiquement appelée par PHPUnit avant CHAQUE test.
     * Elle remet les objets à zéro pour que les tests ne s'influencent pas entre eux.
     */
    protected function setUp(): void
    {
        // createMock(PDO::class) crée un faux objet PDO.
        // Il a les mêmes méthodes que PDO (prepare, execute, etc.)
        // mais elles ne font rien par défaut — on décide nous-mêmes ce qu'elles retournent.
        $this->pdoMock = $this->createMock(PDO::class);

        // On injecte ce faux PDO dans StudentModel (injection de dépendance)
        $this->studentModel = new StudentModel($this->pdoMock);
    }

    // =========================================================================
    // TEST 1 : searchStudent()
    // =========================================================================

    /**
     * Cas testé : on passe un nom valide ("Dupont") à searchStudent().
     * On vérifie que la méthode retourne bien un tableau avec les bons résultats.
     */
    public function testSearchStudent(): void
    {
        // --- DONNÉES FICTIVES ---
        // On définit ce que la BDD est "censée" retourner pour cet étudiant.
        // C'est nous qui inventons ces données, elles ne viennent pas d'une vraie BDD.
        $expectedRows = [[
            'ID_user'      => 1,
            'Email'        => 'jean.dupont@example.com',
            'status'       => 'etudiant',
            'Name'         => 'Jean',
            'Lastname'     => 'Dupont',
            'Phone_number' => '0612345678',
        ]];

        // --- MOCK DU PDOStatement ---
        // Quand PDO::prepare() est appelé, il retourne normalement un PDOStatement.
        // On crée ici un faux PDOStatement pour contrôler son comportement.
        $stmtMock = $this->createMock(PDOStatement::class);

        // La méthode searchStudent() appelle bindParam() 3 fois (:search1, :search2, :search3).
        // On vérifie que c'est bien le cas avec expects($this->exactly(3)).
        $stmtMock->expects($this->exactly(3))
                 ->method('bindParam')
                 ->willReturn(true); // on simule un bindParam réussi

        // execute() doit être appelé exactement une fois pour lancer la requête SQL
        $stmtMock->expects($this->once())
                 ->method('execute');

        // fetchAll() est appelé pour récupérer les résultats.
        // On lui dit de retourner nos données fictives $expectedRows.
        $stmtMock->expects($this->once())
                 ->method('fetchAll')
                 ->with(PDO::FETCH_ASSOC)   // on vérifie aussi le bon mode de récupération
                 ->willReturn($expectedRows);

        // On configure le faux PDO pour retourner notre faux statement quand prepare() est appelé
        $this->pdoMock->expects($this->once())
                      ->method('prepare')
                      ->willReturn($stmtMock);

        // --- APPEL DE LA MÉTHODE ---
        $result = $this->studentModel->searchStudent('Dupont');

        // --- ASSERTIONS ---
        // assertCount(1, ...) vérifie qu'il y a bien 1 résultat dans le tableau retourné
        $this->assertCount(1, $result);
        // assertEquals vérifie que le nom de famille du résultat est bien "Dupont"
        $this->assertEquals('Dupont', $result[0]['Lastname']);
    }

    // =========================================================================
    // TEST 2 : createStudent()
    // =========================================================================

    /**
     * Cas testé : on crée un étudiant avec des données valides.
     * On vérifie que la méthode :
     *   1. Ouvre une transaction
     *   2. Insère d'abord dans la table Profil
     *   3. Récupère le dernier ID inséré (lastInsertId)
     *   4. Insère ensuite dans la table User_
     *   5. Valide la transaction avec commit()
     */
    public function testCreateStudent(): void
    {
        // --- MOCK POUR L'INSERTION DANS Profil ---
        // Ce statement simule le INSERT INTO Profil (...)
        $stmtProfilMock = $this->createMock(PDOStatement::class);
        // execute() doit être appelé exactement une fois pour insérer le profil
        $stmtProfilMock->expects($this->once())
                       ->method('execute')
                       ->willReturn(true);

        // --- MOCK POUR L'INSERTION DANS User_ ---
        // Ce statement simule le INSERT INTO User_ (...)
        $stmtUserMock = $this->createMock(PDOStatement::class);
        // execute() doit aussi être appelé une fois pour créer l'utilisateur
        $stmtUserMock->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);

        // --- CONFIGURATION DU FAUX PDO ---

        // beginTransaction() doit être appelé au début pour garantir l'intégrité des données
        $this->pdoMock->expects($this->once())
                      ->method('beginTransaction');

        // prepare() est appelé 2 fois : une pour Profil, une pour User_
        // willReturnOnConsecutiveCalls retourne les mocks dans l'ordre des appels
        $this->pdoMock->expects($this->exactly(2))
                      ->method('prepare')
                      ->willReturnOnConsecutiveCalls($stmtProfilMock, $stmtUserMock);

        // lastInsertId() retourne l'ID du profil créé, utilisé ensuite pour lier l'utilisateur
        $this->pdoMock->expects($this->once())
                      ->method('lastInsertId')
                      ->willReturn('42'); // on simule un ID = 42

        // commit() doit être appelé à la fin pour valider toutes les insertions
        $this->pdoMock->expects($this->once())
                      ->method('commit');

        // --- APPEL DE LA MÉTHODE ---
        // Si aucune exception n'est levée, le test passe automatiquement
        $this->studentModel->createStudent(
            'alice@example.com',
            'password',
            'Alice',
            'Martin',
            '0600000000'
        );
    }

    // =========================================================================
    // TEST 3 : updateStudent()
    // =========================================================================

    /**
     * Cas testé : on met à jour les informations d'un étudiant existant.
     * On vérifie que la méthode retourne true et que les 3 requêtes SQL
     * (SELECT + UPDATE Profil + UPDATE User_) sont bien exécutées.
     */
    public function testUpdateStudent(): void
    {
        // --- MOCK POUR LE SELECT (récupération de l'ID_profil) ---
        // Avant de mettre à jour, la méthode cherche l'ID_profil lié à l'utilisateur
        $stmtIdMock = $this->createMock(PDOStatement::class);
        $stmtIdMock->method('execute');
        // fetchColumn() retourne la première colonne du résultat, ici l'ID_profil = 10
        $stmtIdMock->method('fetchColumn')->willReturn(10);

        // --- MOCK POUR L'UPDATE DE LA TABLE Profil ---
        $stmtProfilMock = $this->createMock(PDOStatement::class);
        $stmtProfilMock->method('execute')->willReturn(true);

        // --- MOCK POUR L'UPDATE DE LA TABLE User_ ---
        $stmtUserMock = $this->createMock(PDOStatement::class);
        $stmtUserMock->method('execute')->willReturn(true);

        // --- CONFIGURATION DU FAUX PDO ---
        $this->pdoMock->expects($this->once())
                      ->method('beginTransaction');

        // prepare() est appelé 3 fois : SELECT + UPDATE Profil + UPDATE User_
        $this->pdoMock->expects($this->exactly(3))
                      ->method('prepare')
                      ->willReturnOnConsecutiveCalls($stmtIdMock, $stmtProfilMock, $stmtUserMock);

        // commit() valide toutes les modifications
        $this->pdoMock->expects($this->once())
                      ->method('commit');

        // --- APPEL DE LA MÉTHODE ---
        $result = $this->studentModel->updateStudent(
            1,
            'new@email.com',
            'Alice',
            'Martin',
            '0600000001'
        );

        // --- ASSERTION ---
        // La méthode doit retourner true pour signaler que la mise à jour a réussi
        $this->assertTrue($result);
    }

    // =========================================================================
    // TEST 4 : deleteStudent()
    // =========================================================================

    /**
     * Cas testé : on supprime un étudiant existant qui n'a pas de photo.
     * On vérifie que la méthode :
     *   1. Tente de récupérer la photo (null ici, donc rien à supprimer)
     *   2. Récupère l'ID_profil
     *   3. Supprime les données dans les tables liées (Apply, Wishlist, Evaluate)
     *   4. Supprime l'utilisateur dans User_
     *   5. Supprime le profil dans Profil
     *   6. Valide avec commit() et retourne true
     */
    public function testDeleteStudent(): void
    {
        // --- MOCK POUR LA RÉCUPÉRATION DE LA PHOTO ---
        // On simule qu'il n'y a pas de photo (null), donc unlink() ne sera pas appelé
        $stmtPhotoMock = $this->createMock(PDOStatement::class);
        $stmtPhotoMock->method('execute');
        $stmtPhotoMock->method('fetchColumn')->willReturn(null); // pas de photo

        // --- MOCK POUR LA RÉCUPÉRATION DE L'ID_profil ---
        $stmtProfilIdMock = $this->createMock(PDOStatement::class);
        $stmtProfilIdMock->method('execute');
        $stmtProfilIdMock->method('fetchColumn')->willReturn(7); // ID_profil = 7

        // --- MOCKS POUR LA SUPPRESSION DES TABLES LIÉES ---
        // Ces 3 tables référencent l'utilisateur et doivent être nettoyées avant de supprimer User_
        $stmtApplyMock = $this->createMock(PDOStatement::class);
        $stmtApplyMock->method('execute');

        $stmtWishlistMock = $this->createMock(PDOStatement::class);
        $stmtWishlistMock->method('execute');

        $stmtEvaluateMock = $this->createMock(PDOStatement::class);
        $stmtEvaluateMock->method('execute');

        // --- MOCK POUR LA SUPPRESSION DANS User_ ---
        $stmtUserDelMock = $this->createMock(PDOStatement::class);
        $stmtUserDelMock->method('execute');

        // --- MOCK POUR LA SUPPRESSION DANS Profil ---
        $stmtProfilDelMock = $this->createMock(PDOStatement::class);
        $stmtProfilDelMock->method('execute');

        // --- CONFIGURATION DU FAUX PDO ---
        $this->pdoMock->expects($this->once())
                      ->method('beginTransaction');

        // prepare() est appelé 7 fois au total, dans cet ordre :
        // 1. SELECT photo | 2. SELECT ID_profil | 3. DELETE Apply
        // 4. DELETE Wishlist | 5. DELETE Evaluate | 6. DELETE User_ | 7. DELETE Profil
        $this->pdoMock->expects($this->exactly(7))
                      ->method('prepare')
                      ->willReturnOnConsecutiveCalls(
                          $stmtPhotoMock,    // 1. récupération photo
                          $stmtProfilIdMock, // 2. récupération ID_profil
                          $stmtApplyMock,    // 3. suppression Apply
                          $stmtWishlistMock, // 4. suppression Wishlist
                          $stmtEvaluateMock, // 5. suppression Evaluate
                          $stmtUserDelMock,  // 6. suppression User_
                          $stmtProfilDelMock // 7. suppression Profil
                      );

        // commit() valide toutes les suppressions en une seule fois
        $this->pdoMock->expects($this->once())
                      ->method('commit');

        // --- APPEL DE LA MÉTHODE ---
        $result = $this->studentModel->deleteStudent(1);

        // --- ASSERTION ---
        // La méthode doit retourner true pour signaler que la suppression a réussi
        $this->assertTrue($result);
    }

    // =========================================================================
    // TEST 5 : isStudent()
    // =========================================================================

    /**
     * Cas testé : l'utilisateur avec l'ID 1 a le rôle 1 (étudiant).
     * On vérifie que la méthode retourne bien true dans ce cas.
     */
    public function testIsStudent(): void
    {
        // --- MOCK DU PDOStatement ---
        $stmtMock = $this->createMock(PDOStatement::class);

        // bindParam() lie l'ID utilisateur au paramètre :id de la requête SQL
        $stmtMock->method('bindParam')->willReturn(true);

        // execute() lance la requête SELECT
        $stmtMock->method('execute');

        // fetch() retourne la ligne trouvée en base.
        // On simule un utilisateur avec ID_role = 1 (c'est le rôle "étudiant")
        $stmtMock->method('fetch')
                 ->with(PDO::FETCH_ASSOC)
                 ->willReturn(['ID_role' => 1]);

        // Le faux PDO retourne notre faux statement lors de l'appel à prepare()
        $this->pdoMock->expects($this->once())
                      ->method('prepare')
                      ->willReturn($stmtMock);

        // --- APPEL DE LA MÉTHODE ---
        $result = $this->studentModel->isStudent(1);

        // --- ASSERTION ---
        // ID_role = 1 signifie que c'est un étudiant, donc la méthode doit retourner true
        $this->assertTrue($result);
    }
}
