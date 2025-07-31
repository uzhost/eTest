<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$success = '';
$error = '';

// Handle balance top-up
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['topup_amount'])) {
    $amount = floatval($_POST['topup_amount']);

    if ($amount > 0) {
        $pdo->beginTransaction();
        try {
            // Add to balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $userId]);

            // Log the transaction
            $stmt = $pdo->prepare("INSERT INTO balance_history (user_id, amount, type, description) VALUES (?, ?, 'topup', ?)");
            $stmt->execute([$userId, $amount, 'Manual top-up']);

            $pdo->commit();
            $_SESSION['balance'] += $amount;
            $success = "✅ Balance successfully topped up by {$amount} UZS.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "❌ Failed to top up balance. Try again.";
        }
    } else {
        $error = "⚠️ Please enter a valid amount.";
    }
}

// Get updated balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$userId]);
$balance = $stmt->fetchColumn();
$_SESSION['balance'] = $balance;

// Get history
$stmt = $pdo->prepare("SELECT amount, type, description, created_at FROM balance_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$userId]);
$history = $stmt->fetchAll();
?>

<?php include '../header.php'; ?>

<div class="container mt-5" style="max-width: 700px;">
  <div class="card shadow-sm p-4">
    <h3 class="mb-4">💰 Balance Management</h3>

    <div class="mb-3">
      <strong>Current Balance:</strong>
      <span class="badge bg-success"><?= number_format($balance, 0, '.', ' ') ?> UZS</span>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
      <div class="col-md-8">
        <label for="topup_amount" class="form-label">Enter Top-up Amount (UZS):</label>
        <input type="number" class="form-control" name="topup_amount" id="topup_amount" min="1000" step="1000" required>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Top Up</button>
      </div>
    </form>

    <hr class="my-4">

    <h5>🧾 Recent Transactions</h5>
    <?php if (count($history)): ?>
      <table class="table table-striped mt-3">
        <thead>
          <tr>
            <th>Type</th>
            <th>Amount (UZS)</th>
            <th>Description</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($history as $entry): ?>
            <tr>
              <td>
                <span class="badge <?= $entry['type'] == 'topup' ? 'bg-success' : 'bg-danger' ?>">
                  <?= ucfirst($entry['type']) ?>
                </span>
              </td>
              <td><?= number_format($entry['amount'], 0, '.', ' ') ?></td>
              <td><?= htmlspecialchars($entry['description']) ?></td>
              <td><?= date('Y-m-d H:i', strtotime($entry['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="text-muted">No transactions yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php include '../footer.php'; ?>
