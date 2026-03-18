<?php

require 'connexion.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("INSERT INTO User_ (Email) VALUES (?)");
    $stmt->execute([$email]);
}
if (isset($_POST['mdp'])) {
    $mdp = $_POST['mdp'];
    $stmt = $pdo->prepare("INSERT INTO User_ (Password) VALUES (?)");
    $stmt->execute([$mdp]);
}
?>