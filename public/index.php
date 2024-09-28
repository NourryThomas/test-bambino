<?php
session_start();

// Si l'utilisateur est connecté, redirige-le vers la liste des auteurs
if (isset($_SESSION['token_key'])) {
    header('Location: /views/authors.php');
    exit();
} else {
    // Sinon, redirige vers la page de connexion
    header('Location: /views/login.php');
    exit();
}
