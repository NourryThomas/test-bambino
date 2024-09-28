<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User connected ? 
if (isset($_SESSION['token'])) {
    header('Location: /views/dashboard.php');
    exit();
} else {
    header('Location: /views/login.php');
    exit();
}
