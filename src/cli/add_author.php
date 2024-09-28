<?php

if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande.");
}

// Récupérer le token depuis les arguments de la ligne de commande
$options = getopt("", ["token:"]);
$token = $options['token'] ?? null;

if (!$token) {
    die("Le token d'authentification est requis. Utilisation : php add_author.php --token=TON_TOKEN\n");
}

// Fonction pour valider un format de date (YYYY-MM-DD) et vérifier si elle est antérieure à aujourd'hui
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    $now = new DateTime(); // Date actuelle

    // Vérifier que la date est valide et qu'elle est antérieure à aujourd'hui
    return $d && $d->format($format) === $date && $d < $now;
}

// Fonction pour valider un nom (seules les lettres et les espaces sont acceptés)
function validateName($name) {
    return preg_match("/^[a-zA-Z\s]+$/", $name);
}

// Fonction pour valider le sexe (male/female)
function validateGender($gender) {
    return in_array(strtolower($gender), ['male', 'female']);
}

// Saisie avec validation du prénom
do {
    echo "Entrez le prénom de l'auteur : ";
    $firstName = trim(fgets(STDIN));

    if (!validateName($firstName)) {
        echo "Le prénom n'est pas valide. Il ne doit contenir que des lettres et des espaces.\n";
    }
} while (!validateName($firstName));

// Saisie avec validation du nom
do {
    echo "Entrez le nom de l'auteur : ";
    $lastName = trim(fgets(STDIN));

    if (!validateName($lastName)) {
        echo "Le nom n'est pas valide. Il ne doit contenir que des lettres et des espaces.\n";
    }
} while (!validateName($lastName));

// Saisie avec validation de la date de naissance
do {
    echo "Entrez la date de naissance (format : YYYY-MM-DD) : ";
    $birthday = trim(fgets(STDIN));

    if (!validateDate($birthday)) {
        echo "La date de naissance n'est pas valide. Elle doit être au format YYYY-MM-DD et antérieure à aujourd'hui.\n";
    }
} while (!validateDate($birthday));

// Saisie de la biographie (facultative, sans validation stricte)
echo "Entrez la biographie de l'auteur (facultative) : ";
$biography = trim(fgets(STDIN));

// Saisie avec validation du sexe
do {
    echo "Entrez le sexe de l'auteur (male/female) : ";
    $gender = trim(fgets(STDIN));

    if (!validateGender($gender)) {
        echo "Le sexe n'est pas valide. Seules les valeurs 'male' ou 'female' sont acceptées.\n";
    }
} while (!validateGender($gender));

// Saisie avec validation du lieu de naissance
do {
    echo "Entrez le lieu de naissance : ";
    $placeOfBirth = trim(fgets(STDIN));

    if (empty($placeOfBirth)) {
        echo "Le lieu de naissance est obligatoire.\n";
    }
} while (empty($placeOfBirth));

// Préparer les données pour l'API
$data = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'birthday' => $birthday . 'T00:00:00.000Z', // Formater la date pour inclure l'heure
    'biography' => $biography,
    'gender' => strtolower($gender), // Convertir en minuscules pour être cohérent avec l'API
    'place_of_birth' => $placeOfBirth,
];

// Appel à l'API pour ajouter l'auteur
$apiUrl = 'https://candidate-testing.com/api/v2/authors';
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    echo "Auteur ajouté avec succès.\n";
} else {
    echo "Erreur lors de l'ajout de l'auteur. Code HTTP : " . $httpCode . "\n";
    echo "Réponse de l'API : " . $response . "\n";
}
