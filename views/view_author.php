<?php
// Check if the session has not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is authenticated
if (!isset($_SESSION['token'])) {
    header('Location: /index.php');
    exit();
}

// Check if the author ID is passed via GET
if (!isset($_GET['id'])) {
    die("No author specified.");
}

$authorId = $_GET['id'];

// Function to fetch the author's information
function fetchAuthorData($authorId, $token) {
    $apiUrl = 'https://candidate-testing.com/api/v2/authors/' . $authorId;
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
        die('Error fetching author information. HTTP Code: ' . $httpCode);
    }

    return json_decode($response, true);
}

// Call the API to fetch the author's information
$author = fetchAuthorData($authorId, $_SESSION['token']);
$date = new DateTime(htmlspecialchars($author['birthday']));
$birthday = $date->format('m/d/Y');

// Check if books are available
$books = isset($author['books']) ? $author['books'] : [];

// Handle success or error messages
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <!-- Display success or error messages -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Author's information card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Author Information</h5>
                    <p><strong>First Name:</strong> <?php echo htmlspecialchars($author['first_name']); ?></p>
                    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($author['last_name']); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo $birthday ?></p>
                    <p><strong>Biography:</strong> <?php echo htmlspecialchars($author['biography']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($author['gender']); ?></p>
                    <p><strong>Place of Birth:</strong> <?php echo htmlspecialchars($author['place_of_birth']); ?></p>
                </div>
            </div>
        </div>

        <!-- Author's associated books card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Books by the Author</h5>
                    <?php if (!empty($books)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($books as $book): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($book['title']); ?>
                                    <form action="?page=delete_book" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <input type="hidden" name="author_id" value="<?php echo $authorId; ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No books associated with this author.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
