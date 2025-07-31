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
$amount = 0;

// Handle new top-up form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['topup_amount'])) {
    $amount = floatval($_POST['topup_amount']);

    if ($amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO balance_requests (user_id, amount) VALUES (?, ?)");
        $stmt->execute([$userId, $amount]);

        $newInvoiceId = $pdo->lastInsertId();
        header("Location: balance.php?invoice=" . $newInvoiceId);
        exit;
    } else {
        $error = "⚠️ Please enter a valid amount (greater than 0).";
    }
}

// Get current balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$userId]);
$balance = $stmt->fetchColumn();
$_SESSION['balance'] = $balance;

// Fetch last 10 balance requests
$stmt = $pdo->prepare("SELECT id, amount, status, created_at FROM balance_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$userId]);
$requests = $stmt->fetchAll();

// Load selected invoice if available
$selectedInvoice = null;
if (isset($_GET['invoice'])) {
    $invoiceId = intval($_GET['invoice']);
    $stmt = $pdo->prepare("SELECT id, amount FROM balance_requests WHERE id = ? AND user_id = ?");
    $stmt->execute([$invoiceId, $userId]);
    $selectedInvoice = $stmt->fetch();
}
?>

<?php include '../header.php'; ?>

<div class="container mt-5" style="max-width: 700px;">
  <div class="card shadow-sm p-4">
    <h3 class="mb-4">💰 Top Up Balance</h3>

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
        <label for="topup_amount" class="form-label">Enter Amount to Top Up (UZS):</label>
        <input type="number" class="form-control" name="topup_amount" id="topup_amount" min="1000" step="1000" required>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Send Request</button>
      </div>
    </form>

    <?php if ($selectedInvoice): ?>
      <div class="alert alert-info mt-4">
        <h5>💳 Payment Instructions</h5>
        <p>Send <strong><?= number_format($selectedInvoice['amount'], 0, '.', ' ') ?> UZS</strong> to:</p>
        <ul>
          <li><strong>UzCard:</strong> 8600 1234 5678 9012</li>
          <li><strong>Humo:</strong> 9860 1234 5678 9012</li>
          <li><strong>Payme / Click:</strong> +998 90 123 45 67</li>
        </ul>
        <p>📌 <strong>Memo (Comment):</strong> <code>eTest.club: <?= htmlspecialchars($username) ?> Pay#<?= $selectedInvoice['id'] ?></code></p>
        <small>Please make sure to use this memo exactly. Admin will confirm it within 12 hours.</small>
      </div>
    <?php endif; ?>

    <hr class="my-4">

    <h5>📄 Your Top-Up History</h5>
    <table class="table table-sm table-striped mt-3">
      <thead>
        <tr>
          <th>#</th>
          <th>Amount (UZS)</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($requests): ?>
          <?php foreach ($requests as $row): ?>
            <tr class="<?= (isset($selectedInvoice['id']) && $selectedInvoice['id'] == $row['id']) ? 'table-info' : '' ?>">
              <td>#<?= $row['id'] ?></td>
              <td>
                <a href="?invoice=<?= $row['id'] ?>" class="text-decoration-none">
                  <?= number_format($row['amount'], 0, '.', ' ') ?>
                </a>
              </td>
              <td>
                <span class="badge 
                  <?= $row['status'] === 'approved' ? 'bg-success' : ($row['status'] === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                  <?= ucfirst($row['status']) ?>
                </span>
              </td>
              <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4" class="text-muted">No requests yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../footer.php'; ?>
