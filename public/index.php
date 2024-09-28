<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur est connecté, redirige-le vers la liste des auteurs
if (isset($_SESSION['token'])) {
    header('Location: /views/dashboard.php');
    exit();
} else {
    // Sinon, redirige vers la page de connexion
    header('Location: /views/login.php');
    exit();
}
