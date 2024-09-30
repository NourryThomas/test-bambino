<?php
// Load configurations, API functions, and helpers
require_once '../src/config.php';
require_once '../src/api.php';
require_once '../src/helpers.php';

// Check if the user is authenticated
isAuthenticated(); // Use the helper function to ensure authentication

// Fetch the list of authors
$authorsResponse = fetchAuthorsList(); // Use the API function from api.php

if (!$authorsResponse['success']) {
    // Handle error fetching authors
    $errorMessage = $authorsResponse['error'];
    $authors = [];
} else {
    $authors = $authorsResponse['data'];
}

// Include the shared header
include 'shared/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">List of Authors</h1>

    <!-- Display error message if there was an issue fetching authors -->
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date of Birth</th>
            <th>Place of Birth</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php if (!empty($authors)): ?>
                <?php foreach ($authors as $author):
                    $date = new DateTime(htmlspecialchars($author['birthday']));
                    $author_birthday = $date->format('m/d/Y');
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($author['id']); ?></td>
                        <td><?php echo htmlspecialchars($author['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($author['last_name']); ?></td>
                        <td><?php echo $author_birthday; ?></td>
                        <td><?php echo htmlspecialchars($author['place_of_birth']); ?></td>
                        <td><?php echo htmlspecialchars($author['gender']); ?></td>
                        <td>
                            <!-- Button to view author details -->
                            <a href="?page=view_author&id=<?php echo htmlspecialchars($author['id']); ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> View
                            </a>

                            <!-- Form to delete an author -->
                            <form action="?page=delete_author" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this author?');">
                                <input type="hidden" name="author_id" value="<?php echo htmlspecialchars($author['id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No authors available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Include the shared footer
include 'shared/footer.php'; 
?>
