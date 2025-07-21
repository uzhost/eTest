<?php
session_start();
require_once 'config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grammar Test</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">🎓 English Grammar Test</h2>

  <?php if (!isset($_SESSION['user_id'])): ?>
    <div class="alert alert-warning">Please <a href="user/login.php">login</a> or <a href="user/register.php">register</a> to take the test.</div>
  <?php else: ?>
    <form action="tests/start_test.php" method="post" class="card p-4 shadow-sm">
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

      <button type="submit" class="btn btn-success">▶ Start Test</button>
    </form>
  <?php endif; ?>

</div>
</body>
</html>
