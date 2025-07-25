<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}

$difficulty = $_POST['difficulty'] ?? '';
$category = $_POST['category'] ?? '';

$sql = "SELECT * FROM questions WHERE 1";
$params = [];

if ($difficulty && $difficulty !== 'all') {
  $sql .= " AND difficulty = ?";
  $params[] = $difficulty;
}
if (!empty($category)) {
  $sql .= " AND category = ?";
  $params[] = $category;
}

$sql .= " ORDER BY RAND() LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$questions = $stmt->fetchAll();

$totalPages = ceil(count($questions) / 10);
$currentPage = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$start = ($currentPage - 1) * 10;
$currentQuestions = array_slice($questions, $start, 10);

include '../header.php';
?>

<div class="container py-4">
  <h3 class="mb-4">📝 Grammar Test (50 Questions)</h3>

  <?php if (count($questions) === 0): ?>
    <div class="alert alert-warning">No questions found for selected filters.</div>
    <a href="index.php" class="btn btn-secondary">⬅ Back</a>
  <?php else: ?>
    <form action="submit_test.php" method="post">
      <input type="hidden" name="difficulty" value="<?= htmlspecialchars($difficulty) ?>">
      <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">

      <?php foreach ($currentQuestions as $i => $q): ?>
        <div class="card mb-3 shadow-sm">
          <div class="card-body">
            <p><strong>Q<?= $start + $i + 1 ?>:</strong> <?= htmlspecialchars($q['question']) ?></p>
            <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
              <div class="form-check">
                <input type="radio"
                       name="q<?= $q['id'] ?>"
                       value="option_<?= $opt ?>"
                       class="form-check-input"
                       id="q<?= $q['id'] ?>_<?= $opt ?>"
                       required>
                <label class="form-check-label" for="q<?= $q['id'] ?>_<?= $opt ?>">
                  <?= htmlspecialchars($q["option_$opt"]) ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="d-flex justify-content-between align-items-center">
        <div>
          <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>" class="btn btn-outline-primary">⬅ Previous</a>
          <?php endif; ?>
        </div>

        <div>
          Page <?= $currentPage ?> of <?= $totalPages ?>
        </div>

        <div>
          <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>" class="btn btn-outline-primary">Next ➡</a>
          <?php else: ?>
            <button class="btn btn-success">✅ Submit Test</button>
          <?php endif; ?>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
