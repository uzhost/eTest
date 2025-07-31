<?php
require_once '../config/db.php';
session_start();

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

<?php include '../header.php'; ?>

<style>
  body {
    background: linear-gradient(135deg, #f0f4f8, #e0eafc);
    font-family: 'Inter', sans-serif;
  }

  .register-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    padding: 40px;
    margin-top: 60px;
    transition: transform 0.2s ease;
  }

  .register-card:hover {
    transform: translateY(-4px);
  }

  .form-label {
    font-weight: 500;
  }

  .btn-primary {
    background-color: #007bff;
    border: none;
    transition: background-color 0.3s;
  }

  .btn-primary:hover {
    background-color: #0056b3;
  }

  .page-title {
    font-weight: 700;
    color: #333;
  }

  .form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    border-color: #80bdff;
  }

  a {
    color: #007bff;
    text-decoration: none;
  }

  a:hover {
    text-decoration: underline;
  }
</style>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="register-card animate__animated animate__fadeInUp">
        <h2 class="mb-4 text-center page-title">📝 Create Your Account</h2>

        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="username" class="form-label">👤 Username</label>
            <input type="text" name="username" class="form-control" id="username" required>
            <div class="invalid-feedback">Please enter your username.</div>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">📧 Email</label>
            <input type="email" name="email" class="form-control" id="email" required>
            <div class="invalid-feedback">Please enter a valid email address.</div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">🔒 Password</label>
            <input type="password" name="password" class="form-control" id="password" required minlength="6">
            <div class="invalid-feedback">Password must be at least 6 characters.</div>
          </div>

          <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="mt-3 text-center">
          Already have an account? <a href="login.php">Login here</a>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  // Bootstrap client-side validation
  (() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>

<?php include '../footer.php'; ?>
