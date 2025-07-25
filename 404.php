<?php
require_once 'header.php';

// Log the missing URL
file_put_contents('404_log.txt', $_SERVER['REQUEST_URI'] . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
?>

<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">
  <div class="text-center">
    <img src="https://cdn-icons-png.flaticon.com/512/2748/2748558.png" alt="404 Illustration" style="max-width: 200px;" class="mb-4">
    
    <h1 class="display-3 fw-bold text-danger">Oops! 404</h1>
    <p class="lead text-muted">The page you're looking for doesn't exist or has been moved.</p>

    <a href="/" class="btn btn-primary btn-lg mt-3">⬅ Return to Home</a>
    <a href="contact.php" class="btn btn-outline-secondary btn-lg mt-3 ms-2">📩 Contact Support</a>
  </div>
</div>

<?php require_once 'footer.php'; ?>
