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

// Save to `results` table
$stmt = $pdo->prepare("INSERT INTO results (user_id, score, total_questions, correct_answers) VALUES (?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $score, $total, $correct]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Test Result</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>🎯 Your Result</h2>
  <p><strong>Total Questions:</strong> <?= htmlspecialchars($total) ?></p>
  <p><strong>Correct:</strong> <?= htmlspecialchars($correct) ?></p>
  <p><strong>Score:</strong> <?= htmlspecialchars($score) ?>/100</p>
  <a href="index.php" class="btn btn-primary">🔁 Try Again</a>
</div>
</body>
</html>
