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
  <style>
    footer {
      margin-top: 40px;
      padding: 10px 0;
      background: #f8f9fa;
      text-align: center;
      border-top: 1px solid #ddd;
    }
  </style>
  <script>
    let currentPage = 1;
    const questionsPerPage = 10;
    let answers = {};

    function showPage(page) {
      const allQuestions = document.querySelectorAll('.question-card');
      allQuestions.forEach((el, idx) => {
        el.style.display = Math.floor(idx / questionsPerPage) + 1 === page ? 'block' : 'none';
      });
      document.getElementById('page-indicator').textContent = `Page ${page} of ${totalPages}`;
      document.getElementById('prevBtn').disabled = page === 1;
      document.getElementById('nextBtn').disabled = page === totalPages;
      currentPage = page;
      restoreAnswers();
    }

    function nextPage() {
      saveCurrentAnswers();
      if (currentPage < totalPages) showPage(currentPage + 1);
    }

    function prevPage() {
      saveCurrentAnswers();
      if (currentPage > 1) showPage(currentPage - 1);
    }

    function saveCurrentAnswers() {
      document.querySelectorAll('.question-card').forEach(card => {
        if (card.style.display === 'block') {
          const radios = card.querySelectorAll('input[type=radio]');
          radios.forEach(radio => {
            if (radio.checked) {
              answers[radio.name] = radio.value;
            }
          });
        }
      });
    }

    function restoreAnswers() {
      document.querySelectorAll('.question-card').forEach(card => {
        const radios = card.querySelectorAll('input[type=radio]');
        radios.forEach(radio => {
          if (answers[radio.name] === radio.value) {
            radio.checked = true;
          }
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
      startTimer(30 * 60); // 30 minutes
    });
  </script>
</head>
<body>
<header class="bg-dark text-white p-3 mb-4">
  <div class="container d-flex justify-content-between align-items-center">
    <h4 class="mb-0">Grammar Test</h4>
    <div>⏱ Time Left: <span id="timer">30:00</span></div>
  </div>
</header>

<div class="container">
  <?php if (count($questions) === 0): ?>
    <div class="alert alert-warning">No questions found for selected filters.</div>
    <a href="index.php" class="btn btn-secondary">⬅ Back</a>
  <?php else: ?>
    <form action="submit_test.php" method="post" id="testForm">
      <?php foreach ($questions as $i => $q): ?>
        <div class="card mb-3 question-card" style="display: none;">
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
        <button type="button" class="btn btn-outline-secondary" onclick="prevPage()" id="prevBtn">⬅ Previous</button>
        <span id="page-indicator">Page 1</span>
        <button type="button" class="btn btn-outline-secondary" onclick="nextPage()" id="nextBtn">Next ➡</button>
      </div>

      <div class="text-center mt-4">
        <button type="button" onclick="submitTest()" class="btn btn-success">✅ Submit Test</button>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php require_once '../footer.php'; ?>
