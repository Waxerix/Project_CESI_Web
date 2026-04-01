<?php

require_once __DIR__ . '/../Models/UserModel.php';

define('COOKIE_DUREE', 7 * 24 * 3600);
define('COOKIE_NOM', 'remember_token');

class AccessController
{
    private $twig;
    private $pdo;

    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->pdo  = $pdo;
    }

    // -------------------------------------------------------------------------
    // Session
    // -------------------------------------------------------------------------

    public function demarrerSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => false,      // ← passer à true en HTTPS (production)
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    // -------------------------------------------------------------------------
    // Connexion
    // -------------------------------------------------------------------------

    /**
     * Tente de connecter l'utilisateur.
     *
     * SÉCURITÉ :
     *  - Le mot de passe est comparé via password_verify() (hash bcrypt en base).
     *  - Si "Se souvenir de moi" est coché, un token aléatoire est généré,
     *    son hash SHA-256 est stocké en base avec une date d'expiration,
     *    et la valeur brute est placée dans le cookie (jamais le hash).
     */
    public function connecter(string $email, string $motDePasse, bool $sesouvenir = false): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT ID_user, Email, Password, ID_role
             FROM User_
             WHERE Email = :email'
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        // --- FIX : password_verify() au lieu d'une comparaison directe -------
        if (!$user || !password_verify($motDePasse, $user['Password'])) {
            // Message volontairement vague pour ne pas révéler si l'email existe
            return ['succes' => false, 'message' => 'Email ou mot de passe incorrect.'];
        }

        $this->demarrerSession();
        session_regenerate_id(true);

        $_SESSION['user_id']     = $user['ID_user'];
        $_SESSION['user_pseudo'] = $user['Email'];
        $_SESSION['user_admin']  = $user['ID_role'];
        $_SESSION['connecte_le'] = time();

        if ($sesouvenir) {
            $this->creerTokenSouvenir((int) $user['ID_user']);
        }

        return [
            'succes'  => true,
            'message' => 'Connexion réussie.',
            'user'    => [
                'id'     => $user['ID_user'],
                'pseudo' => $user['Email'],
                'admin'  => (bool) $user['ID_role'],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Déconnexion
    // -------------------------------------------------------------------------

    public function deconnecter(): void
    {
        $this->demarrerSession();

        // Invalider le token "remember me" en base avant de supprimer le cookie
        if (!empty($_COOKIE[COOKIE_NOM])) {
            $this->supprimerTokenSouvenir($_COOKIE[COOKIE_NOM]);
        }

        $_SESSION = [];

        // --- FIX : syntaxe tableau cohérente pour setcookie() -----------------
        if (ini_get('session.use_cookies')) {
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        setcookie(COOKIE_NOM, '', [
            'expires'  => time() - 42000,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_destroy();
    }

    // -------------------------------------------------------------------------
    // Vérification de l'état de connexion
    // -------------------------------------------------------------------------

    /**
     * SÉCURITÉ :
     *  - Vérifie d'abord la session PHP.
     *  - Si absent, inspecte le cookie : récupère l'utilisateur ET vérifie
     *    que le hash du token brut correspond bien à un enregistrement valide
     *    et non expiré en base.
     *  - Un cookie forgé (token inconnu) ne donnera jamais accès.
     */
    public function estConnecte(): bool
    {
        $this->demarrerSession();

        if (!empty($_SESSION['user_id'])) {
            return true;
        }

        if (!empty($_COOKIE[COOKIE_NOM])) {
            $tokenBrut = $_COOKIE[COOKIE_NOM];
            $tokenHash = hash('sha256', $tokenBrut);

            $stmt = $this->pdo->prepare(
                'SELECT u.ID_user, u.Email, u.ID_role
                 FROM remember_tokens rt
                 JOIN User_ u ON u.ID_user = rt.ID_user
                 WHERE rt.token_hash = :hash
                   AND rt.expires_at > NOW()'
            );
            $stmt->bindValue(':hash', $tokenHash);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user) {
                // Rotation du token à chaque utilisation (prévention du vol de cookie)
                $this->supprimerTokenSouvenir($tokenBrut);
                $this->creerTokenSouvenir((int) $user['ID_user']);

                session_regenerate_id(true);
                $_SESSION['user_id']     = $user['ID_user'];
                $_SESSION['user_pseudo'] = $user['Email'];
                $_SESSION['user_admin']  = (bool) $user['ID_role'];
                $_SESSION['connecte_le'] = time();
                return true;
            }

            // Token invalide ou expiré → on supprime le cookie
            setcookie(COOKIE_NOM, '', [
                'expires'  => time() - 42000,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // Gardes d'accès
    // -------------------------------------------------------------------------

    public function exigerConnexion(string $redirection = '/connexion'): void
    {
        if (!$this->estConnecte()) {
            header('Location: ' . $redirection);
            exit;
        }
    }

    public function exigerAdmin(string $redirection = '/'): void
    {
        $this->exigerConnexion();
        if (empty($_SESSION['user_admin'])) {
            header('Location: ' . $redirection);
            exit;
        }
    }

    // -------------------------------------------------------------------------
    // Utilisateur courant
    // -------------------------------------------------------------------------

    public function utilisateurCourant(): array
    {
        $this->demarrerSession();
        return [
            'id'     => $_SESSION['user_id']    ?? null,
            'pseudo' => $_SESSION['user_pseudo'] ?? null,
            'admin'  => $_SESSION['user_admin']  ?? false,
        ];
    }

    // -------------------------------------------------------------------------
    // Point d'entrée HTTP (anciennement "test")
    // -------------------------------------------------------------------------

    /**
     * --- FIX : renommé "afficherConnexion()" et plus d'auto-instanciation ---
     * --- FIX : lit $_POST['motDePasse'] qui correspond au champ du formulaire -
     */
    public function afficherConnexion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['email'], $_POST['motDePasse'])
        ) {
            $sesouvenir = !empty($_POST['sesouvenir']);
            $resultat   = $this->connecter(
                $_POST['email'],
                $_POST['motDePasse'],
                $sesouvenir
            );

            if ($resultat['succes']) {
                header('Location: /');
                exit;
            }

            echo $this->twig->render('connexion.html.twig', [
                'erreur' => $resultat['message'],
            ]);
            return;
        }

        echo $this->twig->render('connexion.html.twig', []);
    }

    // -------------------------------------------------------------------------
    // Helpers privés — gestion des tokens "Se souvenir de moi"
    // -------------------------------------------------------------------------

    /**
     * Génère un token brut, stocke son hash en base, pose le cookie.
     */
    private function creerTokenSouvenir(int $userId): void
    {
        $tokenBrut = bin2hex(random_bytes(32));   // 64 caractères hex
        $tokenHash = hash('sha256', $tokenBrut);  // ce qu'on stocke en base
        $expiration = date('Y-m-d H:i:s', time() + COOKIE_DUREE);

        $stmt = $this->pdo->prepare(
            'INSERT INTO remember_tokens (ID_user, token_hash, expires_at)
             VALUES (:user, :hash, :exp)'
        );
        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':hash', $tokenHash);
        $stmt->bindValue(':exp',  $expiration);
        $stmt->execute();

        setcookie(COOKIE_NOM, $tokenBrut, [
            'expires'  => time() + COOKIE_DUREE,
            'path'     => '/',
            'secure'   => false,   // ← passer à true en HTTPS
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    /**
     * Supprime le token brut (via son hash) de la base de données.
     */
    private function supprimerTokenSouvenir(string $tokenBrut): void
    {
        $tokenHash = hash('sha256', $tokenBrut);
        $stmt = $this->pdo->prepare(
            'DELETE FROM remember_tokens WHERE token_hash = :hash'
        );
        $stmt->bindValue(':hash', $tokenHash);
        $stmt->execute();
    }

}