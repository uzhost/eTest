<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

// Delete logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$id]);
    header("Location: questions.php");
    exit;
}

// Filtering logic
$difficultyFilter = $_GET['difficulty'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

$query = "SELECT * FROM questions WHERE 1=1";
$params = [];

if ($difficultyFilter) {
    $query .= " AND difficulty = ?";
    $params[] = $difficultyFilter;
}
if ($categoryFilter) {
    $query .= " AND category = ?";
    $params[] = $categoryFilter;
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For dropdown options
$difficulties = ['easy', 'medium', 'hard'];
$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM questions ORDER BY category");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>🧾 All Questions</h4>
    <div>
      <a href="dashboard.php" class="btn btn-outline-dark btn-sm">← Dashboard</a>
      <a href="add_questions.php" class="btn btn-primary btn-sm">➕ Add New Question</a>
    </div>
  </div>

  <form method="get" class="row g-2 mb-4">
    <div class="col-md-3">
      <select name="difficulty" class="form-select">
        <option value="">All Difficulties</option>
        <?php foreach ($difficulties as $level): ?>
          <option value="<?= $level ?>" <?= $difficultyFilter === $level ? 'selected' : '' ?>>
            <?= ucfirst($level) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat) ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>>
            <?= ucfirst($cat) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <button type="submit" class="btn btn-secondary">Filter</button>
      <a href="questions.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Question</th>
          <th>Answer</th>
          <th>Difficulty</th>
          <th>Category</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($questions) > 0): ?>
          <?php foreach ($questions as $row): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars(substr($row['question'], 0, 60)) ?>...</td>
              <td><?= strtoupper(str_replace("option_", "", $row['correct_answer'])) ?></td>
              <td><?= htmlspecialchars($row['difficulty']) ?></td>
              <td><?= htmlspecialchars($row['category']) ?></td>
              <td>
                <a href="edit_question.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                <a href="questions.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this question?')">🗑️</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center text-muted">No questions found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'footer.php'; ?>
