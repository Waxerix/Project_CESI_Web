<?php
// migrate_passwords.php — à supprimer après utilisation !
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/core/Database.php';

$db  = new App\core\Database();
$pdo = $db->connect();

$users = $pdo->query('SELECT ID_user, Password FROM User_')->fetchAll();

foreach ($users as $user) {
    // Ne migre que les mots de passe pas encore hashés
    if (password_get_info($user['Password'])['algo'] === null) {
        $hash = password_hash($user['Password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE User_ SET Password = :hash WHERE ID_user = :id');
        $stmt->execute([':hash' => $hash, ':id' => $user['ID_user']]);
        echo "Migré : user {$user['ID_user']}\n";
    }
}

echo "Terminé.\n";