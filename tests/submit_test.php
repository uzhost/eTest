<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

include '../header.php';

$user_id = $_SESSION['user_id'];

if (empty($_POST)) {
    die("No answers submitted.");
}

$submittedQuestionIds = [];
foreach ($_POST as $key => $value) {
    if (preg_match('/^q(\d+)$/', $key, $matches)) {
        $submittedQuestionIds[] = (int)$matches[1];
    }
}

if (empty($submittedQuestionIds)) {
    
    ?>
    <div class="container mt-5">
      <div class="alert alert-danger text-center shadow-sm p-4">
        <h4 class="mb-3">⚠️ Invalid Submission</h4>
        <p>No valid answers were submitted. Please try taking the test again.</p>
        <a href="index.php" class="btn btn-primary mt-3">🔁 Start A New Test</a>
        <a href="../user/dashboard.php" class="btn btn-outline-secondary mt-3 ms-2">🏠 Return to Dashboard</a>
      </div>
    </div>
    <?php
    include '../footer.php';
    exit;
}


// Fetch correct answers
$placeholders = implode(',', array_fill(0, count($submittedQuestionIds), '?'));
$stmt = $pdo->prepare("SELECT id, question, correct_answer FROM questions WHERE id IN ($placeholders)");
$stmt->execute($submittedQuestionIds);
$questionMap = [];
foreach ($stmt->fetchAll() as $row) {
    $questionMap[$row['id']] = [
        'correct' => $row['correct_answer'],
        'question' => $row['question']
    ];
}

$score = 0;
$correctAnswers = 0;
$details = [];

foreach ($submittedQuestionIds as $qid) {
    $correct = $questionMap[$qid]['correct'] ?? null;
    $userAnswer = $_POST["q$qid"] ?? null;

    if ($userAnswer && $userAnswer === $correct) {
        $score += 2;
        $correctAnswers++;
    }

    $details[] = [
        'question_id' => $qid,
        'question' => $questionMap[$qid]['question'] ?? 'Unknown',
        'user_answer' => $userAnswer,
        'correct_answer' => $correct
    ];
}

$total = count($details);

// Save overall result
$stmt = $pdo->prepare("INSERT INTO results (user_id, score, total_questions, correct_answers, date_taken) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$user_id, $score, $total, $correctAnswers]);
$result_id = $pdo->lastInsertId();

unset($_SESSION['questions'], $_SESSION['test_filters'], $_SESSION['answers']);
?>

<style>
  .progress-bar {
    font-weight: bold;
  }
  .question-card {
    background-color: #f8f9fa;
    border-left: 5px solid #0d6efd;
  }
  .correct-answer {
    background-color: #d1e7dd;
  }
  .wrong-answer {
    background-color: #f8d7da;
  }
  .question-text {
    font-weight: 500;
  }
  .answer-icon {
    font-size: 1.2rem;
    margin-right: 5px;
  }
</style>

<script>
  localStorage.removeItem("testAnswers");
</script>

<div class="container mt-4">
  <div class="text-center mb-4">
    <h4 class="fw-bold">✅ Your Score: <span class="text-success"><?= $score ?></span> / <?= $total * 2 ?></h4>
    <p>
      <span class="text-success">✔ Correct: <strong><?= $correctAnswers ?></strong></span> / <?= $total ?> |
      <span class="text-danger">✘ Incorrect: <?= $total - $correctAnswers ?></span>
    </p>

    <div class="progress mb-3" style="height: 28px;">
      <div class="progress-bar bg-gradient" role="progressbar" style="width: <?= ($score / ($total * 2)) * 100 ?>%;" aria-valuenow="<?= $score ?>" aria-valuemin="0" aria-valuemax="<?= $total * 2 ?>">
        <?= round(($score / ($total * 2)) * 100) ?>%
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-center gap-3 mb-4">
    <a href="index.php" class="btn btn-primary">🔄 Try Another Test</a>
    <a href="../user/dashboard.php" class="btn btn-outline-secondary">🏠 Dashboard</a>
    <button onclick="window.print()" class="btn btn-outline-dark">🖨 Print Result</button>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
      <h5 class="mb-0">📋 Answer Review</h5>
    </div>
    <ul class="list-group list-group-flush">
      <?php foreach ($details as $index => $d): ?>
        <li class="list-group-item question-card mb-2">
          <div class="question-text">Q<?= $index + 1 ?>: <?= htmlspecialchars($d['question']) ?></div>

          <?php if ($d['user_answer'] === $d['correct_answer']): ?>
            <div class="mt-2 correct-answer p-2 rounded">
              <span class="answer-icon text-success">✅</span>
              <strong>Your Answer:</strong> <?= htmlspecialchars($d['user_answer']) ?>
            </div>
          <?php else: ?>
            <div class="mt-2 wrong-answer p-2 rounded">
              <span class="answer-icon text-danger">❌</span>
              <strong>Your Answer:</strong> <?= htmlspecialchars($d['user_answer']) ?: 'No Answer' ?><br>
              <span class="text-success">✅ Correct Answer:</span> <code><?= htmlspecialchars($d['correct_answer']) ?></code>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="text-center mt-4">
    <a href="../user/results.php" class="btn btn-link">📊 View My Full Test History</a>
  </div>
</div>

<?php include '../footer.php'; ?>
