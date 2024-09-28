<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is authenticated
if (!isset($_SESSION['token'])) {
    header('Location: /index.php');
    exit();
}

// Function to fetch the list of authors
function fetchAuthors($token) {
    $apiUrl = 'https://candidate-testing.com/api/v2/authors';
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
        die('Error fetching authors. HTTP Code: ' . $httpCode);
    }

    return json_decode($response, true);
}

// Retrieve the list of authors
$authors = fetchAuthors($_SESSION['token']);
$authorsList = $authors['items'];

// Manage success or error messages
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<div class="container mt-5">
    <h1>Add a New Book</h1>

    <!-- Display success or error messages -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success" role="alert"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <!-- Form to add a book -->
    <form action="/src/add_book_handler.php" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Book Title:</label>
            <input type="text" id="title" name="title" class="form-control" required placeholder="Enter the book title">
        </div>

        <div class="mb-3">
            <label for="release_date" class="form-label">Publication Date:</label>
            <input type="date" id="release_date" name="release_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea id="description" name="description" class="form-control" required placeholder="Enter a description"></textarea>
        </div>

        <div class="mb-3">
            <label for="isbn" class="form-label">ISBN:</label>
            <input type="text" id="isbn" name="isbn" class="form-control" required pattern="\d{10}|\d{13}" placeholder="Enter an ISBN (10 or 13 digits)">
        </div>

        <div class="mb-3">
            <label for="format" class="form-label">Format:</label>
            <input type="text" id="format" name="format" class="form-control" required placeholder="Enter the format (e.g. 'Paperback')">
        </div>

        <div class="mb-3">
            <label for="number_of_pages" class="form-label">Number of Pages:</label>
            <input type="number" id="number_of_pages" name="number_of_pages" class="form-control" required min="1" placeholder="Enter the number of pages">
        </div>

        <div class="mb-3">
            <label for="author" class="form-label">Author:</label>
            <select id="author" name="author_id" class="form-select" required>
                <option value="">Select an author</option>
                <?php foreach ($authorsList as $author): ?>
                    <option value="<?php echo htmlspecialchars($author['id']); ?>">
                        <?php echo htmlspecialchars($author['first_name']) . ' ' . htmlspecialchars($author['last_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Book</button>
    </form>
</div>
