<?php
require_once 'header.php'; // if you have a shared header
?>
<?php
file_put_contents('404_log.txt', $_SERVER['REQUEST_URI'] . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
?>


<div class="text-center mt-5">
  <h1 class="display-4 text-danger">404 - Page Not Found</h1>
  <p class="lead">Sorry, the page you're looking for doesn't exist.</p>
  <a href="/" class="btn btn-primary">⬅ Back to Home</a>
</div>

<?php require_once 'footer.php'; ?>
