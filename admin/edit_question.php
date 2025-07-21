<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch();

if (!$question) {
    echo "Question not found.";
    exit;
}

if (isset($_POST['submit'])) {
    $q = $_POST['question'];
    $a = $_POST['option_a'];
    $b = $_POST['option_b'];
    $c = $_POST['option_c'];
    $d = $_POST['option_d'];
    $ans = $_POST['correct_answer'];
    $diff = $_POST['difficulty'];
    $cat = $_POST['category'];

    $pdo->prepare("UPDATE questions SET question=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_answer=?, difficulty=?, category=? WHERE id=?")
        ->execute([$q, $a, $b, $c, $d, $ans, $diff, $cat, $id]);

    header("Location: questions.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Question</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3>Edit Question</h3>
  <a href="questions.php" class="btn btn-link mb-3">← Back to Questions</a>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Question</label>
      <textarea name="question" class="form-control" required><?= htmlspecialchars($question['question']) ?></textarea>
    </div>
    <?php foreach (['a', 'b', 'c', 'd'] as $key): ?>
      <div class="mb-3">
        <label>Option <?= strtoupper($key) ?></label>
        <input type="text" name="option_<?= $key ?>" class="form-control"
               value="<?= htmlspecialchars($question["option_$key"]) ?>" required>
      </div>
    <?php endforeach; ?>
    <div class="mb-3">
      <label>Correct Answer</label>
      <select name="correct_answer" class="form-control" required>
        <?php foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $opt): ?>
          <option value="<?= $opt ?>" <?= $question['correct_answer'] === $opt ? 'selected' : '' ?>>
            <?= strtoupper(str_replace('option_', 'Option ', $opt)) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Difficulty</label>
      <select name="difficulty" class="form-control" required>
        <?php foreach (['easy', 'medium', 'hard'] as $diff): ?>
          <option value="<?= $diff ?>" <?= $question['difficulty'] === $diff ? 'selected' : '' ?>><?= ucfirst($diff) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Category</label>
      <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($question['category']) ?>" required>
    </div>
    <button type="submit" name="submit" class="btn btn-success">✅ Update Question</button>
  </form>
</div>
</body>
</html>
