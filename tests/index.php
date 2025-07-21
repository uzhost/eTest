<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: user/login.php"); // adjusted path
  exit;
}
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
  <h2>📝 Start English Grammar Test</h2>
  <form action="start_test.php" method="post" class="mt-4">
    <div class="mb-3">
      <label>Choose Difficulty</label>
      <select name="difficulty" class="form-control" required>
        <option value="">-- Select --</option>
        <option value="easy">Easy</option>
        <option value="medium">Medium</option>
        <option value="hard">Hard</option>
        <option value="all">All Levels</option>
      </select>
    </div>
    <div class="mb-3">
      <label>Choose Category</label>
      <select name="category" class="form-control">
        <option value="">-- Any Category --</option>
        <?php
        $stmt = $pdo->query("SELECT DISTINCT category FROM questions");
        while ($row = $stmt->fetch()) {
            echo "<option value=\"{$row['category']}\">" . ucfirst($row['category']) . "</option>";
        }
        ?>
      </select>
    </div>
    <button class="btn btn-primary">🚀 Start Test</button>
  </form>
</div>
</body>
</html>
