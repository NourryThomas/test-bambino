<?php
session_start();

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
        die('Erreur de connexion à l\'API : ' . curl_error($ch));
    }

    $responseData = json_decode($response, true);

    if ($httpCode === 200 && isset($responseData['token_key'])) {
        $_SESSION['token_key'] = $responseData['token_key'];
        header('Location: /views/authors.php');
        exit();
    } elseif ($httpCode === 403) {
        $error = "Identifiants incorrects. Veuillez réessayer.";
    } else {
        $error = "Erreur de connexion. Code HTTP : " . $httpCode;
    }

    header('Location: /views/login.php?error=' . urlencode($error));
    exit();
}
