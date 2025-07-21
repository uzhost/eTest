<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

if (isset($_POST['submit'])) {
    $question = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];
    $difficulty = $_POST['difficulty'];
    $category = $_POST['category'];

    $stmt = $pdo->prepare("INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, difficulty, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty, $category]);

    $success = "Question added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Question</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3>Add New Question</h3>
  <a href="dashboard.php" class="btn btn-link mb-3">← Back to Dashboard</a>

  <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Question</label>
      <textarea name="question" class="form-control" required></textarea>
    </div>
    <div class="mb-3 row">
      <div class="col">
        <label>Option A</label>
        <input type="text" name="option_a" class="form-control" required>
      </div>
      <div class="col">
        <label>Option B</label>
        <input type="text" name="option_b" class="form-control" required>
      </div>
    </div>
    <div class="mb-3 row">
      <div class="col">
        <label>Option C</label>
        <input type="text" name="option_c" class="form-control" required>
      </div>
      <div class="col">
        <label>Option D</label>
        <input type="text" name="option_d" class="form-control" required>
      </div>
    </div>
    <div class="mb-3">
      <label>Correct Answer</label>
      <select name="correct_answer" class="form-control" required>
        <option value="option_a">Option A</option>
        <option value="option_b">Option B</option>
        <option value="option_c">Option C</option>
        <option value="option_d">Option D</option>
      </select>
    </div>
    <div class="mb-3">
      <label>Difficulty</label>
      <select name="difficulty" class="form-control" required>
        <option>easy</option>
        <option>medium</option>
        <option>hard</option>
      </select>
    </div>
    <div class="mb-3">
      <label>Category</label>
      <input type="text" name="category" class="form-control" placeholder="e.g., present tense" required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Add Question</button>
  </form>
</div>
</body>
</html>
