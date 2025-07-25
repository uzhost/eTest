<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}

// Store answers from previous page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($_POST as $key => $val) {
    if (strpos($key, 'q') === 0) {
      $_SESSION['answers'][$key] = $val;
    }
  }
}

// Get filters from first request or session
if (isset($_POST['difficulty']) || isset($_POST['category'])) {
  $_SESSION['test_filters'] = [
    'difficulty' => $_POST['difficulty'] ?? '',
    'category'   => $_POST['category'] ?? '',
  ];
}

$filters = $_SESSION['test_filters'] ?? ['difficulty' => '', 'category' => ''];
$difficulty = $filters['difficulty'];
$category = $filters['category'];

// Fetch questions
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

// Split pages
$totalPages = ceil(count($questions) / 10);
$currentPage = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$start = ($currentPage - 1) * 10;
$currentQuestions = array_slice($questions, $start, 10);

// Store full list of question IDs once
if (!isset($_SESSION['question_ids'])) {
  $_SESSION['question_ids'] = array_column($questions, 'id');
}

include '../header.php';
?>

<div class="container py-4">
  <h3 class="mb-4">📝 Grammar Test (50 Questions)</h3>

  <?php if (count($questions) === 0): ?>
    <div class="alert alert-warning">No questions found for selected filters.</div>
    <a href="index.php" class="btn btn-secondary">⬅ Back</a>
  <?php else: ?>
    <form action="start_test.php?page=<?= $currentPage + 1 ?>" method="post">
      <?php foreach ($currentQuestions as $i => $q): ?>
        <div class="card mb-3 shadow-sm">
          <div class="card-body">
            <p><strong>Q<?= $start + $i + 1 ?>:</strong> <?= htmlspecialchars($q['question']) ?></p>
            <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
              <?php
                $qid = 'q' . $q['id'];
                $saved = $_SESSION['answers'][$qid] ?? '';
                $checked = ($saved === "option_$opt") ? 'checked' : '';
              ?>
              <div class="form-check">
                <input type="radio"
                       name="<?= $qid ?>"
                       value="option_<?= $opt ?>"
                       class="form-check-input"
                       id="<?= $qid ?>_<?= $opt ?>"
                       <?= $checked ?>>
                <label class="form-check-label" for="<?= $qid ?>_<?= $opt ?>">
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
            <a href="start_test.php?page=<?= $currentPage - 1 ?>" class="btn btn-outline-primary">⬅ Previous</a>
          <?php endif; ?>
        </div>

        <div>
          Page <?= $currentPage ?> of <?= $totalPages ?>
        </div>

        <div>
          <?php if ($currentPage < $totalPages): ?>
            <button class="btn btn-primary">Next ➡</button>
          <?php else: ?>
            <form action="submit_test.php" method="post">
              <button class="btn btn-success">✅ Submit Test</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
