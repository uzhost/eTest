<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$questions = $_SESSION['questions'] ?? [];

$score = 0;
$total = count($questions);
$details = [];

foreach ($questions as $q) {
    $qid = $q['id'];
    $correct = $q['correct_answer'];
    $userAnswer = $_POST["q$qid"] ?? null;

    if ($userAnswer === $correct) {
        $score += 2; // 2 points per correct answer
    }

    $details[] = [
        'question_id' => $qid,
        'user_answer' => $userAnswer,
        'correct_answer' => $correct
    ];
}

// Save results to DB
$stmt = $pdo->prepare("INSERT INTO results (user_id, score, total_questions, taken_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$user_id, $score, $total]);
$result_id = $pdo->lastInsertId();

// Optionally save detailed responses
foreach ($details as $d) {
    $stmt = $pdo->prepare("INSERT INTO result_details (result_id, question_id, user_answer, correct_answer) VALUES (?, ?, ?, ?)");
    $stmt->execute([$result_id, $d['question_id'], $d['user_answer'], $d['correct_answer']]);
}

// Clear used questions from session
unset($_SESSION['questions']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Test Result</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="bg-success text-white py-3 mb-4">
  <div class="container">
    <h3 class="mb-0">🎉 Test Completed</h3>
  </div>
</header>

<div class="container">
  <div class="alert alert-info text-center">
    <h4>Your Score: <?= $score ?> / <?= $total * 2 ?></h4>
    <p>Correct Answers: <?= $score / 2 ?> out of <?= $total ?></p>
  </div>

  <div class="text-center mt-4">
    <a href="index.php" class="btn btn-primary">📘 Take Another Test</a>
    <a href="../user/dashboard.php" class="btn btn-outline-secondary">🏠 Go to Dashboard</a>
  </div>
</div>

<?php require_once '../footer.php'; ?>
</body>
</html>
