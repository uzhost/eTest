<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$email = $_SESSION['email'] ?? 'example@mail.com';
$balance = $_SESSION['balance'] ?? 0;

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
  <title>User Panel - eTest</title>
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
      color: #fff !important;
    }

    .dashboard-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      padding: 30px;
      animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .info-box {
      background: #e9f5ff;
      border-left: 4px solid #007bff;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    footer {
      margin-top: 40px;
      text-align: center;
      color: #666;
      font-size: 14px;
    }

    @media (max-width: 576px) {
      .dashboard-card {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top shadow">
  <div class="container">
    <a class="navbar-brand" href="#">eTest Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon bg-light"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="../tests/start_test.php">Take Test</a></li>
        <li class="nav-item"><a class="nav-link" href="results.php">Results</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="dashboard-card mx-auto" style="max-width: 700px;">
    <h3 class="mb-4">👋 Hello, <?= htmlspecialchars($username) ?>!</h3>

    <div class="info-box">
      📧 <strong>Email:</strong> <?= htmlspecialchars($email) ?><br>
      💰 <strong>Balance:</strong> <?= number_format($balance, 0, '.', ' ') ?> UZS
    </div>

    <div class="info-box">
      🧮 <strong>Total Attempts:</strong> <?= $totalAttempts ?><br>
      <?php if ($lastResult): ?>
        ✅ <strong>Last Score:</strong> <?= $lastResult['score'] ?>/100 (<?= $lastResult['correct_answers'] ?>/<?= $lastResult['total_questions'] ?>)<br>
        🗓️ <strong>Date Taken:</strong> <?= date("Y-m-d H:i", strtotime($lastResult['date_taken'])) ?>
      <?php else: ?>
        ⏳ No tests taken yet.
      <?php endif; ?>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-4">
      <a href="../tests/start_test.php" class="btn btn-success w-100 w-sm-auto">📝 Take a New Test</a>
      <a href="results.php" class="btn btn-outline-primary w-100 w-sm-auto">📊 View Results</a>
    </div>
  </div>
</div>

<footer class="mt-5">
  <p>&copy; <?= date('Y') ?> eTest Platform. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
