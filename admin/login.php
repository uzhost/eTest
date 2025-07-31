<?php
session_start();
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = hash('sha256', $_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['admin'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['is_admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Invalid credentials. Please try again.";
    }
}

include 'header.php';
?>

<style>
body {
    background: linear-gradient(135deg, #e3f2fd, #ffffff);
    min-height: 100vh;
    display: flex;
    align-items: center;
}
.login-card {
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
}
h3 {
    font-weight: 600;
}
</style>

<div class="container">
  <div class="row justify-content-center w-100">
    <div class="col-md-5">
      <div class="login-card mt-5">
        <h3 class="text-center mb-4">🔐 Admin Login</h3>
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
