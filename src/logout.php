<?php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the active session
header('Location: /views/login.php'); // Redirect to the login page
exit();
