<?php
session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['token'])) {
    header('Location: /views/login.php');
    exit();
}

// Vérifier si l'ID de l'auteur est passé via GET
if (!isset($_GET['id'])) {
    die("Aucun auteur spécifié.");
}

$authorId = $_GET['id'];

// Fonction pour récupérer les informations de l'auteur
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
        die('Erreur lors de la récupération des informations de l\'auteur. Code HTTP : ' . $httpCode);
    }

    return json_decode($response, true);
}

// Appel à l'API pour récupérer les informations de l'auteur
$author = fetchAuthorData($authorId, $_SESSION['token']);

// Vérifier si des livres sont disponibles
$books = isset($author['books']) ? $author['books'] : [];

// Gestion des messages de succès ou d'erreur
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Auteur</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <!-- Affichage des messages de succès ou d'erreur -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Carte des informations de l'auteur -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Informations de l'Auteur</h5>
                    <p><strong>Prénom :</strong> <?php echo htmlspecialchars($author['first_name']); ?></p>
                    <p><strong>Nom :</strong> <?php echo htmlspecialchars($author['last_name']); ?></p>
                    <p><strong>Date de naissance :</strong> <?php echo htmlspecialchars($author['birthday']); ?></p>
                    <p><strong>Biographie :</strong> <?php echo htmlspecialchars($author['biography']); ?></p>
                    <p><strong>Sexe :</strong> <?php echo htmlspecialchars($author['gender']); ?></p>
                    <p><strong>Lieu de naissance :</strong> <?php echo htmlspecialchars($author['place_of_birth']); ?></p>
                </div>
            </div>
        </div>

        <!-- Carte des livres associés à l'auteur -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Livres de l'Auteur</h5>
                    <?php if (!empty($books)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($books as $book): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($book['title']); ?>
                                    <form action="/src/delete_book.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?');">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <input type="hidden" name="author_id" value="<?php echo $authorId; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Aucun livre associé à cet auteur.</p>
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
