<?php
require_once 'config/db.php';
$total = 0;
$correct = 0;

foreach ($_POST as $qid => $answer) {
    $id = intval(str_replace("q", "", $qid));
    $stmt = $pdo->prepare("SELECT correct_answer FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row && $row['correct_answer'] === $answer) {
        $correct++;
    }
    $total++;
}

$score = $correct * 2;
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
  <p><strong>Total Questions:</strong> <?= $total ?></p>
  <p><strong>Correct:</strong> <?= $correct ?></p>
  <p><strong>Score:</strong> <?= $score ?>/100</p>
  <a href="index.php" class="btn btn-primary">🔁 Try Again</a>
</div>
</body>
</html>
