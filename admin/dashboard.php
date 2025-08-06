<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

// Counts
$qCount = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$uCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$cCount = $pdo->query("SELECT COUNT(DISTINCT category) FROM questions")->fetchColumn();
$tCount = $pdo->query("SELECT COUNT(*) FROM results")->fetchColumn();

// Chart data (Category distribution)
$catStmt = $pdo->query("SELECT category, COUNT(*) as count FROM questions GROUP BY category");
$categories = [];
$catCounts = [];
while ($row = $catStmt->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = $row['category'];
    $catCounts[] = $row['count'];
}

// Last 5 results
$recentStmt = $pdo->query("SELECT r.*, u.username FROM results r JOIN users u ON r.user_id = u.id ORDER BY r.date_taken DESC LIMIT 5");
$recentTests = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mt-5">
  <h3 class="mb-4">👨‍💼 Admin Dashboard</h3>

  <!-- Stat Cards -->
  <div class="row g-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-start border-primary border-4">
        <div class="card-body">
          <h6 class="text-muted">Total Questions</h6>
          <h3><?= $qCount ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-start border-success border-4">
        <div class="card-body">
          <h6 class="text-muted">Registered Users</h6>
          <h3><?= $uCount ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <h6 class="text-muted">Categories</h6>
          <h3><?= $cCount ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-start border-danger border-4">
        <div class="card-body">
          <h6 class="text-muted">Completed Tests</h6>
          <h3><?= $tCount ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Pie Chart -->
  <!-- Horizontal Bar Chart -->
<div class="mt-5" style="width: 100%; max-width: 600px;">
  <h5 class="mb-3">📊 Question Categories Distribution</h5>
  <canvas id="categoryChart" height="300"></canvas>
</div>

<script>
  const ctx = document.getElementById('categoryChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($categories) ?>,
      datasets: [{
        label: 'Questions per Category',
        data: <?= json_encode($catCounts) ?>,
        backgroundColor: '#007bff',
        borderRadius: 4,
        barThickness: 20
      }]
    },
    options: {
      indexAxis: 'y', // This makes the bar chart horizontal
      responsive: true,
      scales: {
        x: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: { display: false }
      }
    }
  });
</script>


  <!-- Recent Tests -->
  <div class="mt-5">
    <h5 class="mb-3">🕓 Recent Test Activity</h5>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>User</th>
            <th>Score</th>
            <th>Date Taken</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentTests as $test): ?>
            <tr>
              <td><?= htmlspecialchars($test['username']) ?></td>
              <td><?= htmlspecialchars($test['score']) ?> / 100</td>
              <td><?= date('Y-m-d H:i', strtotime($test['date_taken'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Navigation Buttons -->
  <div class="mt-5 d-flex flex-wrap gap-3">
    <a href="upload_questions.php" class="btn btn-outline-primary">📤 Upload Questions</a>
    <a href="questions.php" class="btn btn-outline-success">🗂 View Question Bank</a>
    <a href="users.php" class="btn btn-outline-secondary">👥 Manage Users</a>
    <a href="results.php" class="btn btn-outline-dark">📈 Test Results</a>
    <a href="logout.php" class="btn btn-outline-danger">🚪 Logout</a>
  </div>
</div>

<?php include 'footer.php'; ?>
