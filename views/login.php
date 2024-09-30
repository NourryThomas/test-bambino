<?php
// Set the page title and include global configurations and shared components
$title = 'Login'; 
require_once '../src/config.php'; 
require_once '../src/api.php';
include 'shared/header.php'; 

// Execute the login function if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    login(); // Call the login function directly from api.php
}

// Generate CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a CSRF token for protection
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Login</h2>

                <!-- Error Message Display -->
                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST">
                    <!-- CSRF Protection -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'shared/footer.php'; ?>
