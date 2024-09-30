<?php
// Include global configuration and API functions
require_once '../src/config.php'; 
require_once '../src/api.php';
require_once '../src/helpers.php';

isAuthenticated();

// Check if the author ID is passed via GET
if (!isset($_GET['id'])) {
    die("No author specified.");
}

$authorId = $_GET['id'];

// Fetch the author's information using the API
$authorResponse = fetchAuthorData($authorId, $_SESSION['token']);

if (!$authorResponse['success']) {
    die($authorResponse['error']); // Display error if fetching failed
}

$author = $authorResponse['data'];
$date = new DateTime(htmlspecialchars($author['birthday']));
$birthday = $date->format('m/d/Y');

// Check if books are available
$books = isset($author['books']) ? $author['books'] : [];

// Handle success or error messages
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Set title for the page
$title = 'Author Details';

// Include a header if needed for reusability
include 'shared/header.php'; 
?>

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
                    <p><strong>Date of Birth:</strong> <?php echo $birthday; ?></p>
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

<?php include 'shared/footer.php'; ?>
