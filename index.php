<?php
session_start();
require_once 'config/db.php';

// Get stats
$totalQuestions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalTests = $pdo->query("SELECT COUNT(*) FROM results")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grammar Test | eTest</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .card-style {
      background: #fff;
      padding: 25px;
      border-radius: 18px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s;
    }
    .card-style:hover {
      transform: translateY(-3px);
    }
    .btn-gradient {
      background: linear-gradient(45deg, #00c6ff, #0072ff);
      color: white;
      border: none;
    }
    .btn-gradient:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>
<div class="container py-5">

  <div class="text-center mb-5">
    <h1 class="display-5 fw-bold">🎓 Welcome to eTest Grammar Platform</h1>
    <p class="lead text-muted">Sharpen your English skills with our dynamic and filtered grammar quizzes.</p>
  </div>

  <div class="row text-center mb-4">
    <div class="col-md-4 col-12 mb-3">
      <div class="card card-style">
        <h5>📚 Total Questions</h5>
        <p class="fs-4 text-success"><?= $totalQuestions ?></p>
      </div>
    </div>
    <div class="col-md-4 col-12 mb-3">
      <div class="card card-style">
        <h5>👥 Registered Users</h5>
        <p class="fs-4 text-primary"><?= $totalUsers ?></p>
      </div>
    </div>
    <div class="col-md-4 col-12 mb-3">
      <div class="card card-style">
        <h5>✅ Tests Taken</h5>
        <p class="fs-4 text-danger"><?= $totalTests ?></p>
      </div>
    </div>
  </div>

  <?php if (!isset($_SESSION['user_id'])): ?>
    <div class="alert alert-warning text-center">
      Please <a href="user/login.php">login</a> or <a href="user/register.php">register</a> to take a test.
    </div>
  <?php else: ?>
    <div class="card card-style mb-5">
      <h4 class="mb-3">🧪 Start a New Grammar Test</h4>
      <form action="tests/start_test.php" method="post">
        <div class="mb-3">
          <label for="difficulty" class="form-label">Select Difficulty:</label>
          <select name="difficulty" id="difficulty" class="form-select">
            <option value="all">All Levels</option>
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="category" class="form-label">Select Category:</label>
          <select name="category" id="category" class="form-select">
            <option value="">All Categories</option>
            <?php
            $cats = $pdo->query("SELECT DISTINCT category FROM questions WHERE category IS NOT NULL AND category != ''")->fetchAll();
            foreach ($cats as $cat) {
              echo "<option value=\"{$cat['category']}\">" . htmlspecialchars($cat['category']) . "</option>";
            }
            ?>
          </select>
        </div>

        <button type="submit" class="btn btn-gradient w-100">▶ Start Test</button>
      </form>
    </div>
  <?php endif; ?>

  <hr class="my-5">

  <div class="card card-style mb-5">
    <h4 class="mb-3">📊 Latest Test Results</h4>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>User</th>
            <th>Score</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $pdo->prepare("
            SELECT r.*, u.username 
            FROM results r 
            JOIN users u ON r.user_id = u.id 
            ORDER BY r.id DESC 
            LIMIT 5
          ");
          $stmt->execute();
          $latest = $stmt->fetchAll();

          if ($latest):
            foreach ($latest as $row):
          ?>
            <tr>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><span class="text-success fw-semibold"><?= $row['score'] ?>/<?= $row['total'] ?></span></td>
              <td><?= date("M d, Y H:i", strtotime($row['created_at'] ?? $row['date'] ?? 'now')) ?></td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="3" class="text-center text-muted">No recent results found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include_once 'footer.php'; ?>
