<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if the author has any books
function authorHasBooks($authorId, $token) {
    $apiUrl = 'https://candidate-testing.com/api/v2/authors/' . $authorId . '/books'; // Adjust the endpoint as necessary
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
        return false; // Return false if the API call failed
    }

    $books = json_decode($response, true);
    return !empty($books['items']); // Return true if there are books, false if the list is empty
}

// Function to delete an author
function deleteAuthor($authorId, $token) {
    // First, check if the author has any books
    if (authorHasBooks($authorId, $token)) {
        return "Cannot delete the author. They have associated books.";
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
        return "Error deleting the author. HTTP Code: " . $httpCode;
    }
}

