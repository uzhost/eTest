<?php
require_once '../config/db.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"]);
  $email = trim($_POST["email"]);
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  try {
    $stmt->execute([$username, $email, $password]);
    header("Location: login.php");
    exit;
  } catch (PDOException $e) {
    $error = "⚠️ Username or email already exists.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <h2 class="mb-4 text-center">📝 Create an Account</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="username" class="form-label">👤 Username</label>
          <input type="text" name="username" class="form-control" id="username" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">📧 Email</label>
          <input type="email" name="email" class="form-control" id="email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">🔒 Password</label>
          <input type="password" name="password" class="form-control" id="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
      </form>

      <div class="mt-3 text-center">
        Already have an account? <a href="login.php">Login here</a>
      </div>

    </div>
  </div>
</div>
</body>
</html>
