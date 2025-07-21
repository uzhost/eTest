<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM questions ORDER BY id DESC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Question Bank</h3>
    <div>
      <a href="add_question.php" class="btn btn-success">+ Add Question</a>
      <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Question</th>
        <th>Answer</th>
        <th>Difficulty</th>
        <th>Category</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($questions as $q): ?>
      <tr>
        <td><?= $q['id'] ?></td>
        <td><?= htmlspecialchars($q['question']) ?></td>
        <td><?= $q['correct_answer'] ?></td>
        <td><?= $q['difficulty'] ?></td>
        <td><?= $q['category'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
