<?php

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.");
}

// Retrieve the token from the command-line arguments
$options = getopt("", ["token:"]);
$token = $options['token'] ?? null;

if (!$token) {
    die("Authentication token is required. Usage: php add_author.php --token=YOUR_TOKEN\n");
}

// Function to validate a date format (YYYY-MM-DD) and check if it is before today
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    $now = new DateTime(); // Current date

    // Check that the date is valid and that it is before today
    return $d && $d->format($format) === $date && $d < $now;
}

// Function to validate a name (only letters and spaces are allowed)
function validateName($name) {
    return preg_match("/^[a-zA-Z\s]+$/", $name);
}

// Function to validate gender (male/female)
function validateGender($gender) {
    return in_array(strtolower($gender), ['male', 'female']);
}

// Input with validation for the first name
do {
    echo "Enter the author's first name: ";
    $firstName = trim(fgets(STDIN));

    if (!validateName($firstName)) {
        echo "The first name is not valid. It should only contain letters and spaces.\n";
    }
} while (!validateName($firstName));

// Input with validation for the last name
do {
    echo "Enter the author's last name: ";
    $lastName = trim(fgets(STDIN));

    if (!validateName($lastName)) {
        echo "The last name is not valid. It should only contain letters and spaces.\n";
    }
} while (!validateName($lastName));

// Input with validation for the birthdate
do {
    echo "Enter the birthdate (format: YYYY-MM-DD): ";
    $birthday = trim(fgets(STDIN));

    if (!validateDate($birthday)) {
        echo "The birthdate is not valid. It must be in the format YYYY-MM-DD and before today.\n";
    }
} while (!validateDate($birthday));

// Input for the biography (optional, no strict validation)
echo "Enter the author's biography (optional): ";
$biography = trim(fgets(STDIN));

// Input with validation for gender
do {
    echo "Enter the author's gender (male/female): ";
    $gender = trim(fgets(STDIN));

    if (!validateGender($gender)) {
        echo "The gender is not valid. Only 'male' or 'female' are accepted.\n";
    }
} while (!validateGender($gender));

// Input with validation for place of birth
do {
    echo "Enter the place of birth: ";
    $placeOfBirth = trim(fgets(STDIN));

    if (empty($placeOfBirth)) {
        echo "Place of birth is required.\n";
    }
} while (empty($placeOfBirth));

// Prepare the data for the API
$data = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'birthday' => $birthday . 'T00:00:00.000Z', // Format the date to include time
    'biography' => $biography,
    'gender' => strtolower($gender), // Convert to lowercase for consistency with the API
    'place_of_birth' => $placeOfBirth,
];

// Call the API to add the author
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
    echo "Author successfully added.\n";
} else {
    echo "Error adding the author. HTTP Code: " . $httpCode . "\n";
    echo "API Response: " . $response . "\n";
}
