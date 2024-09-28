<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if the author has any books
function authorHasBooks($authorId, $token) {
    $apiUrl = 'https://candidate-testing.com/api/v2/authors/' . $authorId; // Adjust the endpoint as necessary
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return [
            'hasBooks' => false,
            'error' => "Failed to fetch the author data. HTTP Code: $httpCode"
        ];
    }

    $authorData = json_decode($response, true);

    // Check if the author has any books in the "books" key
    if (!empty($authorData['books'])) {
        return [
            'hasBooks' => true,
            'error' => "Cannot delete the author because he have associated books."
        ];
    }

    return [
        'hasBooks' => false,
        'error' => null // No error
    ];
}

// Function to delete an author
function deleteAuthor($authorId, $token) {
    // First, check if the author has any books
    $bookCheck = authorHasBooks($authorId, $token);

    // If the author has books, return the error message
    if ($bookCheck['hasBooks']) {
        return $bookCheck['error'];
    }

    // If no books are associated, proceed with deletion
    $apiUrl = 'https://candidate-testing.com/api/v2/authors/' . $authorId;
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 204) {
        return "Author successfully deleted.";
    } else {
        return "Error deleting the author. HTTP Code: " . $httpCode ;
    }
}
