<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: /user/login.php");
  exit;
}

$page_title = "Start Grammar Test";
include '../header.php';
?>

<div class="container my-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold text-primary">📝 Start English Grammar Test</h2>
    <p class="text-muted">Choose the difficulty level and category to begin your 50-question test.</p>
  </div>

  <div class="card shadow-sm p-4 mx-auto" style="max-width: 500px;">
    <form action="start_test.php" method="post">
      <div class="mb-3">
        <label for="difficulty" class="form-label">📊 Choose Difficulty <span class="text-danger">*</span></label>
        <select name="difficulty" id="difficulty" class="form-select" required>
          <option value="">-- Select --</option>
          <option value="easy">Easy</option>
          <option value="medium">Medium</option>
          <option value="hard">Hard</option>
          <option value="all">All Levels</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="category" class="form-label">📚 Choose Category (optional)</label>
        <select name="category" id="category" class="form-select">
          <option value="">-- Any Category --</option>
          <?php
          $stmt = $pdo->query("SELECT DISTINCT category FROM questions ORDER BY category ASC");
          while ($row = $stmt->fetch()) {
              $cat = htmlspecialchars($row['category']);
              echo "<option value=\"$cat\">" . ucfirst($cat) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-success btn-lg">🚀 Start Test</button>
      </div>
    </form>
  </div>
</div>

<?php include '../footer.php'; ?>
