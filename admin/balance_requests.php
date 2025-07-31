<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../user/login.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Reached the top of balance_requests.php<br>";

// Handle approval/rejection
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'], $_POST['request_id'])) {
    $requestId = (int) $_POST['request_id'];
    $action = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
    $adminId = $_SESSION['admin_id'] ?? 1;
    $paymentType = $_POST['payment_type'] ?? 'manual';

    if ($action === 'approved') {
        $stmt = $pdo->prepare("SELECT user_id, amount FROM balance_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();

        if ($request) {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")
                ->execute([$request['amount'], $request['user_id']]);
            $pdo->prepare("UPDATE balance_requests SET status = ?, admin_id = ?, payment_method = ?, confirmed_at = NOW() WHERE id = ?")
                ->execute(['approved', $adminId, $paymentType, $requestId]);
            $pdo->commit();
        }
    } else {
        $pdo->prepare("UPDATE balance_requests SET status = ?, admin_id = ?, payment_method = ?, confirmed_at = NOW() WHERE id = ?")
            ->execute(['rejected', $adminId, $paymentType, $requestId]);
    }

    header("Location: balance_requests.php");
    exit;
}

// Fetch all balance requests
$stmt = $pdo->query("
    SELECT br.*, u.username, u.email, a.username AS admin_name
    FROM balance_requests br
    JOIN users u ON br.user_id = u.id
    LEFT JOIN admins a ON br.admin_id = a.id
    ORDER BY br.created_at DESC
");
$requests = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container py-4">
  <h2 class="mb-4">💰 Balance Top-up Requests</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Requested</th>
          <th>Confirmed By</th>
          <th>Payment Type</th>
          <th>Confirmed At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $r): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['username']) ?> (<?= $r['email'] ?>)</td>
          <td><?= number_format($r['amount'], 2) ?> UZS</td>
          <td>
            <?php if ($r['status'] === 'pending'): ?>
              <span class="badge bg-warning text-dark">Pending</span>
            <?php elseif ($r['status'] === 'approved'): ?>
              <span class="badge bg-success">Approved</span>
            <?php else: ?>
              <span class="badge bg-danger">Rejected</span>
            <?php endif; ?>
          </td>
          <td><?= $r['created_at'] ?></td>
          <td><?= $r['admin_name'] ?? '-' ?></td>
          <td><?= $r['payment_method'] ?? '-' ?></td>
          <td><?= $r['confirmed_at'] ?? '-' ?></td>
          <td>
            <?php if ($r['status'] === 'pending'): ?>
            <form method="POST" class="d-flex gap-1 flex-wrap">
              <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
              <select name="payment_method" required class="form-select form-select-sm">
                <option value="">Type</option>
                <option value="click">Click</option>
                <option value="payme">Payme</option>
                <option value="card">Card</option>
                <option value="cash">Cash</option>
                <option value="manual">Manual</option>
              </select>
              <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
              <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
            </form>
            <?php else: ?>
              <em>N/A</em>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'footer.php'; ?>
