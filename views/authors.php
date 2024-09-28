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

// Call the API to retrieve the list of authors
$apiUrl = 'https://candidate-testing.com/api/v2/authors';
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'], 
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die('Error fetching authors. HTTP Code: ' . $httpCode);
}

// Decode the JSON response
$authorsData = json_decode($response, true);

// Retrieve the list of authors (inside the 'items' key)
$authors = $authorsData['items'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List of Authors</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">List of Authors</h1>

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
            <?php foreach ($authors as $author):
                $date = new DateTime(htmlspecialchars($author['birthday']));
                $author_birthday = $date->format('m/d/Y');
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($author['id']); ?></td>
                    <td><?php echo htmlspecialchars($author['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($author['last_name']); ?></td>
                    <td><?php echo $author_birthday ?></td>
                    <td><?php echo htmlspecialchars($author['place_of_birth']); ?></td>
                    <td><?php echo htmlspecialchars($author['gender']); ?></td>
                    <td>
                        <!-- Button to view author details -->

                        <a href="?page=view_author&id=<?php echo $author['id']; ?>" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> View

                        </a>

                        <!-- Form to delete an author -->
                        <form action="?page=delete_author" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this author?');">
                            <input type="hidden" name="author_id" value="<?php echo $author['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS & Popper.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
