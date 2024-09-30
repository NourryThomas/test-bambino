<?php
// Load configurations, API functions, and helpers
require_once '../src/config.php'; 
require_once '../src/api.php';
require_once '../src/helpers.php';

// Check if the user is authenticated
isAuthenticated(); // Use the helper to check authentication

// Page management
$page = isset($_GET['page']) ? $_GET['page'] : 'authors';
$message = "";
$messageType = ""; // To manage success or error messages

// If author deletion is requested
if ($page === 'delete_author' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authorId = $_POST['author_id'];
    $deleteResponse = deleteAuthor($authorId); // Using the API function

    if ($deleteResponse['success']) {
        $message = 'Author successfully deleted.';
        $messageType = 'success';
    } else {
        $message = $deleteResponse['error'];
        $messageType = 'error';
    }

    header('Location: ?page=authors&message=' . urlencode($message) . '&type=' . $messageType);
    exit();
}

// If book deletion is requested
if ($page === 'delete_book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'];
    $authorId = $_POST['author_id'];

    $deleteResponse = deleteBook($bookId); // Using the API function

    if ($deleteResponse['success']) {
        $message = 'Book successfully deleted.';
        $messageType = 'success';
    } else {
        $message = $deleteResponse['error'];
        $messageType = 'error';
    }

    header('Location: ?page=view_author&id=' . $authorId . '&message=' . urlencode($message) . '&type=' . $messageType);
    exit();
}

// Get user information for display
$firstName = $_SESSION['user']['first_name'] ?? 'User';
$lastName = $_SESSION['user']['last_name'] ?? '';
?>

<?php include 'shared/header.php'; // Include shared header ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar section -->
        <nav class="col-md-2 d-none d-md-block bg-dark sidebar" style="height: 100vh;">
            <div class="sidebar-sticky">
                <h4 class="text-center text-white py-3"><?php echo htmlspecialchars($firstName) . ' ' . htmlspecialchars($lastName); ?></h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=authors">
                            <i class="bi bi-people-fill"></i> Authors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=add_book">
                            <i class="bi bi-plus-square-fill"></i> Add Book
                        </a>
                    </li>
                </ul>
                <!-- Logout button at the bottom -->
                <div class="mt-auto">
                    <a href="/views/logout.php" class="btn btn-danger w-100 mt-5">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main content section -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <!-- Display success or error messages -->
            <?php if (!empty($_GET['message'])): ?>
                <?php 
                $alertClass = $_GET['type'] === 'success' ? 'alert-success' : 'alert-danger';
                ?>
                <div class="alert <?php echo $alertClass; ?>" role="alert">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>

            <?php
            // Include the corresponding page based on the menu selection
            switch ($page) {
                case 'authors':
                    include 'authors.php';
                    break;
                case 'books':
                    include 'books.php';
                    break;
                case 'add_book':
                    include 'add_book.php';
                    break;
                case 'view_author':
                    if (isset($_GET['id'])) {
                        include 'view_author.php';
                    } else {
                        echo "<p>Author not found.</p>";
                    }
                    break;
                default:
                    echo "<p>Page not found.</p>";
                    break;
            }
            ?>
        </main>
    </div>
</div>

<?php include 'shared/footer.php'; // Include shared footer ?>
