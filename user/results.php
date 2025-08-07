<?php
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

// Fetch paginated results
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
    <p class="text-muted text-center">
      Showing <strong><?= $offset + 1 ?></strong>–<strong><?= min($offset + $perPage, $totalResults) ?></strong> of <strong><?= $totalResults ?></strong> results
    </p>

    <div class="table-responsive">
      <table class="table table-hover table-bordered align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Score</th>
            <th>Correct</th>
            <th>Total</th>
            <th>Progress</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $i => $res): 
            $score = (int)$res['score'];
            $totalQuestions = (int) $res['total_questions'];
            $totalPoints = $totalQuestions * 2;
            $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;

            $badge = $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
          ?>
            <tr>
              <td><?= $offset + $i + 1 ?></td>
              <td><?= date("Y-m-d H:i", strtotime($res['created_at'])) ?></td>
              <td>
                <span class="badge bg-<?= $badge ?>">
                  <?= $score ?>/<?= $totalPoints ?>
                </span>
              </td>
              <td><?= $res['correct_answers'] ?></td>
              <td><?= $res['total_questions'] ?></td>
              <td>
                <div class="progress" style="height: 20px;">
                  <div class="progress-bar bg-<?= $badge ?>" role="progressbar" style="width: <?= round($percentage) ?>%;">
                    <?= round($percentage) ?>%
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
      <nav>
        <ul class="pagination justify-content-center mt-4 flex-wrap">
          <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=1">« First</a></li>
            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">‹ Prev</a></li>
          <?php endif; ?>

          <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
            <li class="page-item <?= $p == $page ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next ›</a></li>
            <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>">Last »</a></li>
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
