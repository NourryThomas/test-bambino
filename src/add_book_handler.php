<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is authenticated
if (!isset($_SESSION['token'])) {
    header('Location: /index.php');
    exit();
}

// Prepare the data to add a book
$data = [
    'author' => ['id' => $_POST['author_id']],
    'title' => $_POST['title'],
    'release_date' => $_POST['release_date'],
    'description' => $_POST['description'],
    'isbn' => $_POST['isbn'],
    'format' => $_POST['format'],
    'number_of_pages' => (int)$_POST['number_of_pages']
];

// Call the API to add the book
$apiUrl = 'https://candidate-testing.com/api/v2/books';
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201 || $httpCode === 200) {
    header('Location: /views/dashboard.php?page=add_book&success=Book added successfully.');
} else {
    header('Location: /views/dashboard.php?page=add_book&error=Error adding the book.');
}
exit();
