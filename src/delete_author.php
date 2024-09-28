<?php
session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['token'])) {
    header('Location: /views/login.php');
    exit();
}

// Vérifier si l'ID de l'auteur est passé via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['author_id'])) {
    $authorId = $_POST['author_id'];

    // Fonction pour vérifier si l'auteur a des livres associés
    function hasBooks($authorId, $token) {
        $apiUrl = 'https://candidate-testing.com/api/v2/books?author_id=' . $authorId;
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $booksData = json_decode($response, true);
            return !empty($booksData['items']); // Retourne true si des livres sont associés
        } else {
            return false; // En cas d'erreur de requête ou autre, considérer qu'il n'y a pas de livres
        }
    }

    // Vérifier si l'auteur a des livres associés
    if (hasBooks($authorId, $_SESSION['token'])) {
        // Si l'auteur a des livres, redirection avec un message d'erreur
        header('Location: /views/authors.php?error=Cet auteur a des livres associés et ne peut pas être supprimé.');
        exit();
    }

    // Appel à l'API pour supprimer l'auteur
    $apiUrl = 'https://candidate-testing.com/api/v2/authors/' . $authorId;
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token'], 
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 204) {
        // Auteur supprimé avec succès
        header('Location: /views/authors.php?success=Auteur supprimé avec succès.');
        exit();
    } else {
        // Gestion des erreurs
        header('Location: /views/authors.php?error=Erreur lors de la suppression de l\'auteur.');
        exit();
    }
} else {
    // Si aucune ID n'est passée ou si la méthode n'est pas POST, rediriger
    header('Location: /views/authors.php');
    exit();
}
