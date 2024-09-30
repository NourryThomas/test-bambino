<?php

// Redirect to login if the user is not authenticated
function isAuthenticated() {
    if (!isset($_SESSION['token'])) {
        header('Location: /views/login.php');
        exit();
    }
}
/**
 * Send an API request
 * 
 * @param string $url The API URL
 * @param string $method The HTTP method (GET, POST, DELETE, etc.)
 * @param string|null $token The authorization token
 * @param array|null $data Optional data for POST/PUT requests
 * @return array The response including success and data or error messages
 */
function sendApiRequest($url, $method, $token = null, $data = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // Set headers, including authorization if token is provided
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Add POST/PUT data if available
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Parse response based on HTTP code
    if ($httpCode >= 200 && $httpCode < 300) {
        return [
            'success' => true,
            'data' => json_decode($response, true)
        ];
    } else {
        $errorMessage = handleHttpError($httpCode, $response);
        return [
            'success' => false,
            'error' => $errorMessage,
            'httpCode' => $httpCode
        ];
    }
}

/**
 * Handle HTTP error codes and provide a meaningful error message.
 * 
 * @param int $httpCode The HTTP status code returned by the API
 * @param string $response The response body
 * @return string A user-friendly error message
 */
function handleHttpError($httpCode, $response) {
    switch ($httpCode) {
        case 400:
            return 'Bad Request: Please check the data you have provided.';
        case 401:
            return 'Unauthorized: Invalid token or session expired. Please log in again.';
        case 403:
            return 'Forbidden: You do not have permission to perform this action.';
        case 404:
            return 'Not Found: The resource you are looking for does not exist.';
        case 422:
            return 'Unprocessable Entity: Invalid input. Please review the form fields.';
        case 500:
            return 'Internal Server Error: An error occurred on the server. Please try again later.';
        default:
            return 'HTTP Code ' . $httpCode . ': ' . $response;
    }
}

// Function to handle logout
function logout() {
    session_start();
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header('Location: /views/login.php'); // Redirect to login
    exit();
}
