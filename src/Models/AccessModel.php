<?php
namespace App\Models;
use PDO;

define('COOKIE_DUREE', 7 * 24 * 3600);
define('COOKIE_NOM', 'remember_token');
class AccessModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User_ WHERE ID_user = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function getUserByUsername($username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User_ WHERE Email = :username');
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function getUserByUsernameAndPassword($username, $password)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User_ WHERE Email = :username');
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['Password'])) {
            return $user;
        }
        return false;
    }

    public function createUser($email, $password, $admin = 0)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO User_ (Email, Password, ID_role) VALUES (:email, :password, :admin)');
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':admin', $admin, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function deleteUser($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM User_ WHERE ID_user = :id');
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    public function updateUser($id, $email, $password = null, $admin = null)
    {
        $fields = [];
        if ($email) {
            $fields[] = 'Email = :email';
        }
        if ($password) {
            $fields[] = 'Password = :password';
        }
        if ($admin !== null) {
            $fields[] = 'ID_role = :admin';
        }
        if (empty($fields)) {
            return false; // Rien à mettre à jour
        }

        $sql = 'UPDATE User_ SET ' . implode(', ', $fields) . ' WHERE ID_user = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        if ($email) {
            $stmt->bindValue(':email', $email);
        }
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindValue(':password', $hashedPassword);
        }
        if ($admin !== null) {
            $stmt->bindValue(':admin', $admin, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }
    public function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => false,      // ← passer à true en HTTPS (production)
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    public function connect(string $email, string $motDePasse, bool $sesouvenir = false): array
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

        $this->startSession();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['ID_user'];
        $_SESSION['user_pseudo'] = $user['Email'];
        $_SESSION['user_admin'] = $user['ID_role'];
        $_SESSION['connecte_le'] = time();

        if ($sesouvenir) {
            $this->createToken((int) $user['ID_user']);
        }

        return [
            'succes' => true,
            'message' => 'Connexion réussie.',
            'user' => [
                'id' => $user['ID_user'],
                'pseudo' => $user['Email'],
                'admin' => (bool) $user['ID_role'],
            ],
        ];
    }

    public function deconnect(): void
    {
        $this->startSession();

        // Invalider le token "remember me" en base avant de supprimer le cookie
        if (!empty($_COOKIE[COOKIE_NOM])) {
            $this->deleteToken($_COOKIE[COOKIE_NOM]);
        }

        $_SESSION = [];

        // --- FIX : syntaxe tableau cohérente pour setcookie() -----------------
        if (ini_get('session.use_cookies')) {
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        setcookie(COOKIE_NOM, '', [
            'expires' => time() - 42000,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_destroy();
    }

    public function isConnect(): bool
    {
        $this->startSession();

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
                $this->deleteToken($tokenBrut);
                $this->createToken((int) $user['ID_user']);

                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['ID_user'];
                $_SESSION['user_pseudo'] = $user['Email'];
                $_SESSION['user_admin'] = (bool) $user['ID_role'];
                $_SESSION['connecte_le'] = time();
                return true;
            }

            // Token invalide ou expiré → on supprime le cookie
            setcookie(COOKIE_NOM, '', [
                'expires' => time() - 42000,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        return false;
    }

    public function curentUser(): array
    {
        $this->startSession();
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'pseudo' => $_SESSION['user_pseudo'] ?? null,
            'admin' => $_SESSION['user_admin'] ?? false,
        ];
    }

}
