<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit;
}

$total = 0;
$correct = 0;

// Loop through each submitted answer
foreach ($_POST as $qid => $answer) {
    if (strpos($qid, 'q') === 0) {
        $id = intval(substr($qid, 1));
        $stmt = $pdo->prepare("SELECT correct_answer FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row && $row['correct_answer'] === $answer) {
            $correct++;
        }
        $total++;
    }
}

$score = $correct * 2;

// Save to results table
$stmt = $pdo->prepare("INSERT INTO results (user_id, score, total_questions, correct_answers) VALUES (?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $score, $total, $correct]);

require_once '../header.php'; // Make sure this contains <body> tag start
?>

<div class="container mt-5 text-center">
  <div class="card shadow-lg p-4 rounded-4 bg-white">
    <h2 class="mb-4 text-success">🎯 Test Completed!</h2>
    
    <div class="mb-3">
      <span class="badge bg-secondary fs-5">📝 Total Questions: <?= htmlspecialchars($total) ?></span>
    </div>
    
    <div class="mb-3">
      <span class="badge bg-info fs-5">✅ Correct Answers: <?= htmlspecialchars($correct) ?></span>
    </div>
    
    <div class="mb-3">
      <span class="badge bg-warning text-dark fs-5">⭐ Score: <?= htmlspecialchars($score) ?> / 100</span>
    </div>

    <div class="mt-4">
      <a href="../index.php" class="btn btn-primary btn-lg me-2">🔁 Try Again</a>
      <a href="../users/results.php" class="btn btn-outline-secondary btn-lg">📊 View My History</a>
    </div>
  </div>
</div>

<?php require_once '../footer.php'; ?>
