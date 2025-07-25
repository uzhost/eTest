<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

// Count total questions
$qCount = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();

// Count total users
$uCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Count distinct categories
$cCount = $pdo->query("SELECT COUNT(DISTINCT category) FROM questions")->fetchColumn();

// Count completed tests
$tCount = $pdo->query("SELECT COUNT(*) FROM results")->fetchColumn();
?>

<?php include 'header.php'; ?>

<div class="container mt-5">
  <h3 class="mb-4">👨‍💼 Admin Dashboard</h3>

  <div class="row g-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-start border-primary border-4">
        <div class="card-body">
          <h6 class="text-muted">Total Questions</h6>
          <h3><?= $qCount ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-start border-success border-4">
        <div class="card-body">
          <h6 class="text-muted">Registered Users</h6>
          <h3><?= $uCount ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <h6 class="text-muted">Categories</h6>
          <h3><?= $cCount ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-start border-danger border-4">
        <div class="card-body">
          <h6 class="text-muted">Completed Tests</h6>
          <h3><?= $tCount ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-5 d-flex flex-wrap gap-3">
    <a href="upload_questions.php" class="btn btn-outline-primary">📤 Upload Questions</a>
    <a href="questions.php" class="btn btn-outline-success">🗂 View Question Bank</a>
    <a href="users.php" class="btn btn-outline-secondary">👥 Manage Users</a>
    <a href="results.php" class="btn btn-outline-dark">📈 Test Results</a>
    <a href="logout.php" class="btn btn-outline-danger">🚪 Logout</a>
  </div>
</div>

<?php include 'footer.php'; ?>
