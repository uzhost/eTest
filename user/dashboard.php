<?php
session_start();
require_once 'config/db.php';

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>👋 Welcome, <?= htmlspecialchars($username) ?></h2>

  <div class="mt-4">
    <p><strong>Total Attempts:</strong> <?= $totalAttempts ?></p>

    <?php if ($lastResult): ?>
      <p><strong>Last Score:</strong> <?= $lastResult['score'] ?>/100 (<?= $lastResult['correct_answers'] ?>/<?= $lastResult['total_questions'] ?>)</p>
      <p><strong>Date:</strong> <?= date("M d, Y H:i", strtotime($lastResult['date_taken'])) ?></p>
    <?php else: ?>
      <p>No test attempts yet.</p>
    <?php endif; ?>

    <a href="take_test.php" class="btn btn-success mt-3">📝 Take a New Test</a>
    <a href="logout.php" class="btn btn-outline-secondary mt-3 ms-2">Logout</a>
  </div>
</div>
</body>
</html>
