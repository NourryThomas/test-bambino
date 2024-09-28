<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparation des données à envoyer à l'API
    $data = [
        'email' => $email,
        'password' => $password
    ];

    $apiUrl = 'https://candidate-testing.com/api/v2/token'; 

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        // Afficher l'erreur cURL
        $error = 'Erreur de connexion à l\'API : ' . curl_error($ch);
        header('Location: /views/login.php?error=' . urlencode($error));
        exit();
    }

    $responseData = json_decode($response, true);

    if ($httpCode === 200 && isset($responseData['token_key'])) {
        // Connexion réussie, stocker le token dans la session
        session_regenerate_id(true); // Sécurise la session en générant un nouvel ID de session
        $_SESSION['token'] = $responseData['token_key'];
        header('Location: /views/dashboard.php'); // Redirection vers le tableau de bord
        exit();
    } elseif ($httpCode === 403) {
        // Identifiants incorrects
        $error = "Identifiants incorrects. Veuillez réessayer.";
    } else {
        // Gestion d'autres erreurs
        $error = "Erreur de connexion. Code HTTP : " . $httpCode;
    }

    // Redirection vers la page de connexion avec un message d'erreur
    header('Location: /views/login.php?error=' . urlencode($error));
    exit();
}
