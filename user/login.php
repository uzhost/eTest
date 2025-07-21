<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"]);
  $password = $_POST["password"];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user["password"])) {
    // Login success
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "❌ Invalid username or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <h2 class="mb-4 text-center">🔐 Login</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="username" class="form-label">👤 Username</label>
          <input type="text" name="username" class="form-control" id="username" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">🔑 Password</label>
          <input type="password" name="password" class="form-control" id="password" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Login</button>
      </form>

      <div class="mt-3 text-center">
        Don't have an account? <a href="register.php">Register here</a>
      </div>

    </div>
  </div>
</div>
</body>
</html>
