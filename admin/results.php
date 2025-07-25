<?php
require_once '../config/db.php';
session_start();

// Optional: Admin Auth Check
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit;
// }

$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Total count for pagination
$totalStmt = $pdo->query("SELECT COUNT(*) FROM results");
$totalResults = $totalStmt->fetchColumn();
$totalPages = ceil($totalResults / $perPage);

// Get paginated results with user names
$perPage = (int)$perPage;
$offset = (int)$offset;

$sql = "
  SELECT r.*, u.username 
  FROM results r
  JOIN users u ON r.user_id = u.id
  ORDER BY r.created_at DESC
  LIMIT $perPage OFFSET $offset
";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();

?>

<?php include 'header.php'; ?>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📋 All Test Results</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary">⬅ Dashboard</a>
  </div>

  <?php if (!$results): ?>
    <div class="alert alert-info">No results found.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Date</th>
            <th>Score</th>
            <th>Total</th>
            <th>Correct</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $i => $res): ?>
            <tr>
              <td><?= $offset + $i + 1 ?></td>
              <td><?= htmlspecialchars($res['username']) ?></td>
              <td><?= isset($res['created_at']) ? date("Y-m-d H:i", strtotime($res['created_at'])) : 'N/A' ?></td>
              <td><strong><?= $res['score'] ?></strong> / <?= $res['total_questions'] * 2 ?></td>
              <td><?= $res['total_questions'] ?></td>
              <td><?= $res['correct_answers'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

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
</div>

<?php include 'footer.php'; ?>
