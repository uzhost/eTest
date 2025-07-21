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
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "❌ Invalid username or password.";
  }
}
?>

<?php include '../header.php'; ?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <div class="card-style">

        <h2 class="mb-4 text-center">🔐 Login to Your Account</h2>

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
            <div class="input-group">
              <input type="password" name="password" class="form-control" id="password" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">👁️</button>
            </div>
          </div>

          <button type="submit" class="btn btn-success w-100">Login</button>
        </form>

        <div class="mt-3 text-center">
          Don't have an account? <a href="register.php">Register here</a>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  function togglePassword() {
    const passwordInput = document.getElementById("password");
    passwordInput.type = (passwordInput.type === "password") ? "text" : "password";
  }
</script>

<?php include '../footer.php'; ?>
