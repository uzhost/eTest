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

// Extract all submitted question IDs
$submittedQuestionIds = [];
foreach ($_POST as $key => $value) {
    if (preg_match('/^q(\d+)$/', $key, $matches)) {
        $submittedQuestionIds[] = (int)$matches[1];
    }
}

if (empty($submittedQuestionIds)) {
    die("No valid answers submitted.");
}

// Fetch correct answers from DB
$placeholders = implode(',', array_fill(0, count($submittedQuestionIds), '?'));
$stmt = $pdo->prepare("SELECT id, correct_answer FROM questions WHERE id IN ($placeholders)");
$stmt->execute($submittedQuestionIds);
$questionMap = [];
foreach ($stmt->fetchAll() as $row) {
    $questionMap[$row['id']] = $row['correct_answer'];
}

$score = 0;
$correctAnswers = 0;
$details = [];

foreach ($submittedQuestionIds as $qid) {
    $correct = $questionMap[$qid] ?? null;
    $userAnswer = $_POST["q$qid"] ?? null;

    if ($userAnswer && $userAnswer === $correct) {
        $score += 2;
        $correctAnswers++;
    }

    $details[] = [
        'question_id' => $qid,
        'user_answer' => $userAnswer,
        'correct_answer' => $correct
    ];
}

$total = count($details);

// Save overall result
$stmt = $pdo->prepare("INSERT INTO results (user_id, score, total_questions, correct_answers, date_taken) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$user_id, $score, $total, $correctAnswers]);
$result_id = $pdo->lastInsertId();

// Save each answer detail
//foreach ($details as $d) {
    //$stmt = $pdo->prepare("INSERT INTO result_details (result_id, question_id, user_answer, correct_answer) VALUES (?, ?, ?, ?)");
    //$stmt->execute([$result_id, $d['question_id'], $d['user_answer'], $d['correct_answer']]);
//}

// Clear test session data
unset($_SESSION['questions']);
unset($_SESSION['test_filters']);
unset($_SESSION['answers']);  // if used for pagination tracking
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Test Result</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    localStorage.removeItem("testAnswers");
  </script>
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
    <p>Correct Answers: <?= $correctAnswers ?> out of <?= $total ?></p>
  </div>

  <div class="text-center mt-4">
    <a href="index.php" class="btn btn-primary">📘 Take Another Test</a>
    <a href="../user/dashboard.php" class="btn btn-outline-secondary">🏠 Go to Dashboard</a>
  </div>
</div>

<?php include '../footer.php'; ?>
