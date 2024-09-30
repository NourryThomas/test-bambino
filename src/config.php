<?php
// src/config.php

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// API configuration
define('API_URL', 'https://candidate-testing.com/api/v2');

// Retrieve the API token from session
define('API_TOKEN', $_SESSION['token'] ?? null);
