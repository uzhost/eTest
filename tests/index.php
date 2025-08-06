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

<style>
  body {
    background: linear-gradient(135deg, #eef9ff, #e8fbe9);
    font-family: 'Segoe UI', sans-serif;
  }

  .test-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 50px 40px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .test-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
  }

  .form-label {
    font-weight: 600;
    font-size: 1.05rem;
    color: #333;
  }

  .form-select {
    font-size: 1rem;
    padding: 0.75rem;
    border-radius: 10px;
    border: 1px solid #ccc;
  }

  .btn-gradient {
    background: linear-gradient(to right, #28a745, #218838);
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 14px;
    border-radius: 12px;
    border: none;
    transition: 0.3s ease;
  }

  .btn-gradient:hover {
    background: linear-gradient(to right, #218838, #1e7e34);
  }

  .heading-icon {
    font-size: 2.4rem;
    color: #0d6efd;
    margin-bottom: 10px;
  }

  .info-note {
    font-size: 0.9rem;
    color: #666;
    margin-top: 4px;
  }

  .category-example {
    font-size: 0.85rem;
    color: #999;
  }

  .back-link {
    text-decoration: none;
    font-weight: 500;
    color: #444;
    transition: 0.3s ease;
  }

  .back-link:hover {
    color: #0d6efd;
    text-decoration: underline;
  }
</style>

<div class="container my-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold text-primary">Start Your Grammar Test</h2>
    <p class="text-muted">Select the difficulty level and category to begin a personalized 50-question test.</p>
  </div>

  <div class="test-card mx-auto" style="max-width: 560px;">
    <form action="start_test.php" method="post">

      <div class="mb-4">
        <label for="difficulty" class="form-label">📊 Select Difficulty <span class="text-danger">*</span></label>
        <select name="difficulty" id="difficulty" class="form-select" required>
          <option value="">-- Choose Difficulty --</option>
          <option value="easy">🟢 Easy</option>
          <option value="medium">🟡 Medium</option>
          <option value="hard">🔴 Hard</option>
          <option value="all">🌐 All Levels</option>
        </select>
        <div class="info-note">"All Levels" includes a mix of all difficulties.</div>
      </div>

      <div class="mb-4">
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
        <div class="info-note">Examples: <span class="badge bg-light text-dark">Tenses</span>, <span class="badge bg-light text-dark">Articles</span>, <span class="badge bg-light text-dark">Prepositions</span></div>
      </div>

      <div class="d-grid mt-4">
        <button type="submit" class="btn-gradient">🚀 Start Test Now</button>
      </div>

    </form>
  </div>

  <div class="text-center mt-4">
    <a href="../user/dashboard.php" class="back-link">⬅️ Back to Dashboard</a>
  </div>
</div>

<?php include '../footer.php'; ?>
