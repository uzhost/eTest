<?php
require_once '../config/db.php';
require_once 'header.php';

$stmt = $pdo->query("
  SELECT t.*, u.username AS user_name, a.username AS admin_name
  FROM transactions t
  JOIN users u ON t.user_id = u.id
  JOIN admins a ON t.admin_id = a.id
  ORDER BY t.created_at DESC
");
$transactions = $stmt->fetchAll();
?>

<div class="container mt-4">
  <h3>📜 Transaction History</h3>

  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Admin</th>
        <th>Amount ($)</th>
        <th>Date & Time</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($transactions as $i => $tx): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= htmlspecialchars($tx['user_name']) ?></td>
        <td><?= htmlspecialchars($tx['admin_name']) ?></td>
        <td><?= number_format($tx['amount'], 2) ?></td>
        <td><?= $tx['created_at'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>

<?php require_once 'footer.php'; ?>
