<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

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
        $error = 'API connection error: ' . curl_error($ch);
        header('Location: /views/login.php?error=' . urlencode($error));
        exit();
    }

    $responseData = json_decode($response, true);

    if ($httpCode === 200 && isset($responseData['token_key'])) {
        session_regenerate_id(true); // Secure the session

        // Store the token, first name, and last name of the user
        $_SESSION['token'] = $responseData['token_key'];
        $_SESSION["user"]['first_name'] = $responseData["user"]['first_name']; 
        $_SESSION["user"]['last_name'] = $responseData["user"]['last_name'];

        header('Location: /views/dashboard.php');
        exit();
    } elseif ($httpCode === 401) {
        $error = "Incorrect credentials. Please try again.";
    } else {
        $error = "Connection error. HTTP Code: " . $httpCode;
    }

    header('Location: /views/login.php?error=' . urlencode($error));
    exit();
}
