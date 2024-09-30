<?php
// src/api.php

require_once 'helpers.php';

function fetchAuthorData($authorId) {
    if (!API_TOKEN) {
        return [
            'success' => false,
            'error' => 'Authentication token not found.'
        ];
    }

    $apiUrl = API_URL . '/authors/' . $authorId;
    $response = sendApiRequest($apiUrl, 'GET', API_TOKEN);

    if ($response['success']) {
        return [
            'success' => true,
            'data' => $response['data']
        ];
    }

    return [
        'success' => false,
        'error' => 'Error fetching author information: ' . $response['error'],
        'httpCode' => $response['httpCode']
    ];
}

function fetchAuthorsList() {
    if (!API_TOKEN) {
        return [
            'success' => false,
            'error' => 'Authentication token not found.'
        ];
    }

    $apiUrl = API_URL . '/authors';
    $response = sendApiRequest($apiUrl, 'GET', API_TOKEN);

    if ($response['success']) {
        return [
            'success' => true,
            'data' => $response['data']['items'] ?? []
        ];
    }

    return [
        'success' => false,
        'error' => 'Error fetching authors list: ' . $response['error'],
        'httpCode' => $response['httpCode']
    ];
}


function login() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = trim($_POST['password']);

        if (!$email || empty($password)) {
            $error = 'Please provide a valid email and password.';
            header('Location: /views/login.php?error=' . urlencode($error));
            exit();
        }

        $data = ['email' => $email, 'password' => $password];
        $apiUrl = API_URL . '/token';

        $response = sendApiRequest($apiUrl, 'POST', null, $data);

        if ($response['success'] && isset($response['data']['token_key'])) {
            session_regenerate_id(true);
            $_SESSION['token'] = $response['data']['token_key'];
            $_SESSION['user']['first_name'] = $response['data']['user']['first_name'];
            $_SESSION['user']['last_name'] = $response['data']['user']['last_name'];

            header('Location: /views/dashboard.php');
            exit();
        } else {
            $error = $response['error'] ?? 'Connection error.';
            header('Location: /views/login.php?error=' . urlencode($error));
            exit();
        }
    }
}

function deleteBook($bookId) {
    if (!API_TOKEN) {
        return [
            'success' => false,
            'message' => 'Authentication token is missing.'
        ];
    }

    $apiUrl = API_URL . '/books/' . $bookId;
    $response = sendApiRequest($apiUrl, 'DELETE', API_TOKEN);

    if ($response['success']) {
        return [
            'success' => true,
            'message' => 'Book successfully deleted.'
        ];
    }

    return [
        'success' => false,
        'error' => 'Error deleting the book: ' . $response['error'],
        'httpCode' => $response['httpCode']
    ];
}

// Add a new book
function addBook($bookData) {
    if (!API_TOKEN) {
        return [
            'success' => false,
            'error' => 'Authentication token not found.'
        ];
    }

    $apiUrl = API_URL . '/books';
    $response = sendApiRequest($apiUrl, 'POST', API_TOKEN, $bookData);

    if ($response['success']) {
        return [
            'success' => true,
            'data' => $response['data']
        ];
    }

    return [
        'success' => false,
        'error' => 'Error adding the book: ' . $response['error']
    ];
}

// Check if an author has any associated books
function authorHasBooks($authorId) {
    if (!API_TOKEN) {
        return [
            'hasBooks' => false,
            'error' => 'Authentication token not found.'
        ];
    }

    $apiUrl = API_URL . '/authors/' . $authorId;
    $response = sendApiRequest($apiUrl, 'GET', API_TOKEN);

    if (!$response['success']) {
        return [
            'hasBooks' => false,
            'error' => 'Failed to fetch author data: ' . $response['error']
        ];
    }

    if (!empty($response['data']['books'])) {
        return [
            'hasBooks' => true,
            'error' => 'Cannot delete the author because they have associated books.'
        ];
    }

    return [
        'hasBooks' => false,
        'error' => null
    ];
}

function deleteAuthor($authorId) {
    if (!API_TOKEN) {
        return [
            'success' => false,
            'error' => 'Authentication token not found.'
        ];
    }

    $bookCheck = authorHasBooks($authorId);

    if ($bookCheck['hasBooks']) {
        return [
            'success' => false,
            'error' => $bookCheck['error']
        ];
    }

    $apiUrl = API_URL . '/authors/' . $authorId;
    $response = sendApiRequest($apiUrl, 'DELETE', API_TOKEN);

    if ($response['success']) {
        return [
            'success' => true,
            'message' => "Author successfully deleted."
        ];
    }

    return [
        'success' => false,
        'error' => 'Error deleting the author: ' . $response['error'],
        'httpCode' => $response['httpCode']
    ];
}

