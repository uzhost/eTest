<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
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

$_SESSION['questions'] = $questions;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take Test</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    let currentPage = 1;
    const questionsPerPage = 10;
    let answers = {};

    function showPage(page) {
      const allQuestions = document.querySelectorAll('.question-card');
      allQuestions.forEach((el, idx) => {
        el.style.display = Math.floor(idx / questionsPerPage) + 1 === page ? 'block' : 'none';
      });
      document.getElementById('page-indicator').textContent = `Page ${page}`;
      document.getElementById('prevBtn').disabled = page === 1;
      document.getElementById('nextBtn').disabled = page === totalPages;
      currentPage = page;
    }

    function nextPage() {
      saveCurrentAnswers();
      showPage(currentPage + 1);
    }

    function prevPage() {
      saveCurrentAnswers();
      showPage(currentPage - 1);
    }

    function saveCurrentAnswers() {
      document.querySelectorAll('.question-card').forEach(card => {
        const radios = card.querySelectorAll('input[type=radio]:checked');
        radios.forEach(radio => {
          answers[radio.name] = radio.value;
        });
      });
    }

    function submitTest() {
      saveCurrentAnswers();
      const form = document.getElementById('testForm');
      for (const key in answers) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = answers[key];
        form.appendChild(input);
      }
      form.submit();
    }

    function startTimer(seconds) {
      const display = document.getElementById('timer');
      const timer = setInterval(() => {
        const minutes = String(Math.floor(seconds / 60)).padStart(2, '0');
        const secs = String(seconds % 60).padStart(2, '0');
        display.textContent = `${minutes}:${secs}`;
        if (--seconds < 0) {
          clearInterval(timer);
          alert("⏰ Time is up! Submitting your test...");
          submitTest();
        }
      }, 1000);
    }

    document.addEventListener("DOMContentLoaded", () => {
      window.totalPages = Math.ceil(document.querySelectorAll('.question-card').length / questionsPerPage);
      showPage(1);
      startTimer(1800); // 30 minutes
    });
  </script>
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center">
    <h3>📝 Grammar Test (50 Questions)</h3>
    <div class="fw-bold text-danger">⏱ Time Left: <span id="timer">30:00</span></div>
  </div>

  <?php if (count($questions) === 0): ?>
    <div class="alert alert-warning">No questions found for selected filters.</div>
    <a href="index.php" class="btn btn-secondary">⬅ Back</a>
  <?php else: ?>
    <form action="submit_test.php" method="post" id="testForm">
      <?php foreach ($questions as $i => $q): ?>
        <div class="card mb-3 question-card" style="display: none">
          <div class="card-body">
            <p><strong>Q<?= $i + 1 ?>:</strong> <?= htmlspecialchars($q['question']) ?></p>
            <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
              <div class="form-check">
                <input type="radio"
                       name="q<?= $q['id'] ?>"
                       value="option_<?= $opt ?>"
                       class="form-check-input"
                       id="q<?= $q['id'] ?>_<?= $opt ?>">
                <label class="form-check-label" for="q<?= $q['id'] ?>_<?= $opt ?>">
                  <?= htmlspecialchars($q["option_$opt"]) ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="d-flex justify-content-between align-items-center mt-4">
        <button type="button" class="btn btn-secondary" onclick="prevPage()" id="prevBtn">⬅ Previous</button>
        <span id="page-indicator">Page 1</span>
        <button type="button" class="btn btn-secondary" onclick="nextPage()" id="nextBtn">Next ➡</button>
      </div>
      <div class="text-center mt-4">
        <button type="button" onclick="submitTest()" class="btn btn-success">✅ Submit Test</button>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php require_once '../footer.php'; ?>
