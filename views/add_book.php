<?php
// Load configurations, API functions, and helpers
require_once '../src/config.php';
require_once '../src/api.php';
require_once '../src/helpers.php';

// Check if the user is authenticated
isAuthenticated(); // Use the helper function

// Manage success or error messages
$successMessage = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookData = [
        'author' => ['id' => $_POST['author_id']],
        'title' => $_POST['title'],
        'release_date' => $_POST['release_date'],
        'description' => $_POST['description'],
        'isbn' => $_POST['isbn'],
        'format' => $_POST['format'],
        'number_of_pages' => (int)$_POST['number_of_pages']
    ];

    // Call the addBook function from api.php
    $addBookResponse = addBook($bookData);

    if ($addBookResponse['success']) {
        $successMessage = "Book added successfully.";
    } else {
        $errorMessage = "Error adding the book: " . $addBookResponse['error'];
    }
}

// Fetch the list of authors using the existing API function
$authorsResponse = fetchAuthorsList(); // Use the function from api.php

if (!$authorsResponse['success']) {
    // If fetching authors fails, set an error message
    $errorMessage = $authorsResponse['error'];
    $authorsList = [];
} else {
    $authorsList = $authorsResponse['data'];
}
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
    <form action="" method="POST">
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
                <?php if (!empty($authorsList)): ?>
                    <?php foreach ($authorsList as $author): ?>
                        <option value="<?php echo htmlspecialchars($author['id']); ?>">
                            <?php echo htmlspecialchars($author['first_name']) . ' ' . htmlspecialchars($author['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No authors available</option>
                <?php endif; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Book</button>
    </form>
</div>
