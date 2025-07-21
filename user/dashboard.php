<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Get last result
$stmt = $pdo->prepare("SELECT score, correct_answers, total_questions, date_taken FROM results WHERE user_id = ? ORDER BY date_taken DESC LIMIT 1");
$stmt->execute([$userId]);
$lastResult = $stmt->fetch();

// Count total attempts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE user_id = ?");
$stmt->execute([$userId]);
$totalAttempts = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f4f8fb;
      font-family: 'Segoe UI', sans-serif;
    }

    .navbar {
      background-color: #003366;
    }

    .navbar-brand,
    .nav-link {
      color: #ffffff !important;
    }

    .dashboard-card {
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }

    .dashboard-card h2 {
      color: #003366;
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
    }

    .btn-outline-secondary:hover {
      background-color: #f2f2f2;
    }

    footer {
      margin-top: 40px;
      text-align: center;
      color: #888;
    }

    @media (max-width: 576px) {
      .dashboard-card {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand" href="#">eTest Portal</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="start_test.php">Take Test</a></li>
        <li class="nav-item"><a class="nav-link" href="results.php">Results</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="dashboard-card mx-auto" style="max-width: 600px;">
    <h2>👋 Welcome, <?= htmlspecialchars($username) ?></h2>

    <hr>
    <p><strong>Total Attempts:</strong> <?= $totalAttempts ?></p>

    <?php if ($lastResult): ?>
      <p><strong>Last Score:</strong> <?= $lastResult['score'] ?>/100 (<?= $lastResult['correct_answers'] ?>/<?= $lastResult['total_questions'] ?>)</p>
      <p><strong>Date:</strong> <?= date("M d, Y H:i", strtotime($lastResult['date_taken'])) ?></p>
    <?php else: ?>
      <p>No test attempts yet.</p>
    <?php endif; ?>

    <div class="d-flex gap-2 mt-4">
      <a href="start_test.php" class="btn btn-success">📝 Take a New Test</a>
      <a href="results.php" class="btn btn-primary">📊 View Results</a>
    </div>
  </div>
</div>

<footer class="mt-5">
  <p>&copy; <?= date('Y') ?> eTest Platform</p>
</footer>

</body>
</html>
