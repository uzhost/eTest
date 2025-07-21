<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

// Fetch paginated results (bind by name to avoid collision with positional param)
$stmt = $pdo->prepare("
    SELECT * FROM results 
    WHERE user_id = :user_id 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll();
?>

<?php include_once '../header.php'; ?>

<div class="container my-5">
  <h2 class="mb-4 text-center">📊 Your Test History</h2>

  <?php if (count($results) === 0): ?>
    <div class="alert alert-info text-center">You haven't taken any tests yet.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered align-middle text-center">
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
              <td><strong><?= $res['score'] ?>/<?= $res['total_questions'] * 2 ?></strong></td>
              <td><?= $res['total_questions'] ?></td>
              <td><?= $res['correct_answers'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
      <nav>
        <ul class="pagination justify-content-center mt-4">
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
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-outline-primary">⬅ Back to Dashboard</a>
  </div>
</div>

<?php include_once '../footer.php'; ?>
