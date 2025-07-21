<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

// Delete logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$id]);
    header("Location: questions.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Questions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3>All Questions</h3>
  <a href="dashboard.php" class="btn btn-link mb-3">← Back to Dashboard</a>
  <a href="add_question.php" class="btn btn-primary mb-3">➕ Add New Question</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Question</th>
        <th>Answer</th>
        <th>Difficulty</th>
        <th>Category</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $stmt = $pdo->query("SELECT * FROM questions ORDER BY id DESC");
    foreach ($stmt as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars(substr($row['question'], 0, 60)) ?>...</td>
        <td><?= strtoupper(str_replace("option_", "", $row['correct_answer'])) ?></td>
        <td><?= $row['difficulty'] ?></td>
        <td><?= $row['category'] ?></td>
        <td>
          <a href="edit_question.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
          <a href="questions.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
