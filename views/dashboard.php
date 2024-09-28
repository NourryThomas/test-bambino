<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is authenticated
if (!isset($_SESSION['token'])) {
    header('Location: /index.php');
    exit();
}

include '../src/delete_author.php';
include '../src/delete_book.php';

// Page management
$page = isset($_GET['page']) ? $_GET['page'] : 'authors';
$message = "";
$messageType = ""; // New parameter to handle the message type

// If deletion is requested
if ($page === 'delete_author' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authorId = $_POST['author_id'];
    $message = deleteAuthor($authorId, $_SESSION['token']);
    // Determine if deletion succeeded or failed
    $messageType = strpos($message, 'success') !== false ? 'success' : 'error';
    header('Location: ?page=authors&message=' . urlencode($message) . '&type=' . $messageType);
    exit();
}

if ($page === 'delete_book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'];
    $authorId = $_POST['author_id'];

    // Debugging: Display the variable values for verification
    echo "Book ID: " . htmlspecialchars($bookId) . "<br>";
    echo "Author ID: " . htmlspecialchars($authorId) . "<br>";

    // Call the function to delete the book
    $message = deleteBook($bookId, $_SESSION['token']);

    // Determine if deletion succeeded or failed
    $messageType = strpos($message, 'success') !== false ? 'success' : 'error';
    header('Location: ?page=view_author&id=' . $authorId . '&message=' . urlencode($message) . '&type=' . $messageType);
    exit();
}

// Get user information
$firstName = isset($_SESSION["user"]['first_name']) ? $_SESSION["user"]['first_name'] : 'User';
$lastName = isset($_SESSION["user"]['last_name']) ? $_SESSION["user"]['last_name'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            min-width: 250px;
            background-color: #343a40;
            color: white;
            padding: 15px;
            position: fixed;
            height: 100%;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 0;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }
    </style>
</head>
<body>

<!-- Sidebar menu -->
<div class="sidebar d-flex flex-column justify-content-between">
    <div>
        <h4 class="text-center"><?php echo htmlspecialchars($firstName) . ' ' . htmlspecialchars($lastName); ?></h4>
        <hr>
        <a href="?page=authors"><i class="bi bi-people-fill"></i> Authors</a>
        <a href="?page=add_book"><i class="bi bi-plus-square-fill"></i> Add Book</a>
    </div>
    <!-- Logout button at the bottom of the menu -->
    <div class="mt-auto">
        <a href="/src/logout.php" class="btn btn-danger w-100">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <!-- Display success or error messages -->
    <?php if (!empty($_GET['message'])): ?>
        <?php 
        // Check the type of message
        $alertClass = isset($_GET['type']) && $_GET['type'] === 'success' ? 'alert-success' : 'alert-danger';
        ?>
        <div class="alert <?php echo $alertClass; ?>" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    
    <?php
    // Include the page corresponding to the selected menu option
    if ($page == 'authors') {
        include 'authors.php'; // Authors page
    } elseif ($page == 'books') {
        include 'books.php'; // Books page
    } elseif ($page == 'add_book') {
        include 'add_book.php'; // Page to add a book
    } elseif ($page == 'view_author' && isset($_GET['id'])) {
        include 'view_author.php'; // Page to display an author by their ID
    } else {
        echo "<p>Page not found.</p>";
    }
    ?>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
