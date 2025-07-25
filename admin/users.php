<?php
require_once '../config/db.php';
require_once 'header.php';

// Handle top-up form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['amount'])) {
    $userId = intval($_POST['user_id']);
    $amount = floatval($_POST['amount']);

    if ($amount > 0) {
        // 1. Update balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $userId]);

        // 2. Insert transaction
        $adminId = $_SESSION['admin_id'];
        $pdo->prepare("INSERT INTO transactions (user_id, admin_id, amount) VALUES (?, ?, ?)")
            ->execute([$userId, $adminId, $amount]);

        $message = "✅ Balance updated and transaction recorded.";
    } else {
        $error = "❌ Invalid amount.";
    }
}


// Fetch all users
$users = $pdo->query("SELECT id, username, email, balance FROM users ORDER BY id DESC")->fetchAll();
?>

<div class="container mt-4">
  <h3>👥 Users & Balances</h3>

  <?php if (isset($message)): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php elseif (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <table class="table table-bordered table-striped mt-3">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Username</th>
        <th>Email</th>
        <th>Balance ($)</th>
        <th>Top-up</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $index => $user): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= number_format($user['balance'], 2) ?></td>
          <td>
            <form method="post" class="d-flex" style="gap: 5px;">
              <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
              <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm" placeholder="Amount" required>
              <button type="submit" class="btn btn-success btn-sm">💰 Add</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>

<?php require_once 'footer.php'; ?>
