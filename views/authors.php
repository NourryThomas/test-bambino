<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: /views/login.php');
    exit();
}

// Appel à l'API pour récupérer la liste des auteurs
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
    die('Erreur lors de la récupération des auteurs. Code HTTP : ' . $httpCode);
}

// Décoder la réponse JSON
$authorsData = json_decode($response, true);

// Récupérer la liste des auteurs (dans la clé 'items')
$authors = $authorsData['items'];

// Gérer les messages d'erreur ou de succès
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des Auteurs</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Liste des Auteurs</h1>

    <!-- Afficher les messages d'erreur ou de succès -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Date de naissance</th>
            <th>Lieu de naissance</th>
            <th>Genre</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($authors as $author): ?>
            <tr>
                <td><?php echo htmlspecialchars($author['id']); ?></td>
                <td><?php echo htmlspecialchars($author['first_name']); ?></td>
                <td><?php echo htmlspecialchars($author['last_name']); ?></td>
                <td><?php echo htmlspecialchars($author['birthday']); ?></td>
                <td><?php echo htmlspecialchars($author['place_of_birth']); ?></td>
                <td><?php echo htmlspecialchars($author['gender']); ?></td>
                <td>
                    <!-- Bouton pour voir les détails de l'auteur -->
                    <a href="/views/view_author.php?id=<?php echo $author['id']; ?>" class="btn btn-info btn-sm">
                        <i class="bi bi-eye"></i> Voir
                    </a>

                    <!-- Formulaire pour supprimer un auteur -->
                    <form action="/src/delete_author.php" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet auteur ?');">
                        <input type="hidden" name="author_id" value="<?php echo $author['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Supprimer
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
