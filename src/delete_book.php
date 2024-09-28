<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to delete a book
function deleteBook($bookId, $token) {
    $apiUrl = 'https://candidate-testing.com/api/v2/books/' . $bookId;
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
        return "Book successfully deleted.";
    } else {
        return "Error deleting the book. HTTP Code: " . $httpCode;
    }
}
?>
