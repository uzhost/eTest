<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Count total results
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE user_id = ?");
$countStmt->execute([$userId]);
$totalResults = $countStmt->fetchColumn();
$totalPages = ceil($totalResults / $perPage);

// Fetch paginated results
$stmt = $pdo->prepare("SELECT * FROM results WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$userId, $perPage, $offset]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Results</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3>📊 Your Test History</h3>

  <?php if (count($results) === 0): ?>
    <div class="alert alert-info mt-4">No test results found.</div>
  <?php else: ?>
    <table class="table table-bordered table-striped mt-4">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Score</th>
          <th>Total Questions</th>
          <th>Correct Answers</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $i => $res): ?>
          <tr>
            <td><?= $offset + $i + 1 ?></td>
            <td><?= date("Y-m-d H:i", strtotime($res['created_at'])) ?></td>
            <td><?= $res['score'] ?>/<?= $res['total_questions'] * 2 ?></td>
            <td><?= $res['total_questions'] ?></td>
            <td><?= $res['correct_answers'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <nav>
      <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $page - 1 ?>">« Prev</a>
          </li>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <li class="page-item <?= $p == $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $page + 1 ?>">Next »</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>

  <a href="index.php" class="btn btn-primary mt-3">⬅ Back to Home</a>
</div>
</body>
</html>
