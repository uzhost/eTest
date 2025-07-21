<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM questions ORDER BY id DESC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($questions);
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📋 Question Bank <small class="text-muted">(<?= $total ?> total)</small></h3>
    <div>
      <a href="add_questions.php" class="btn btn-success">+ Add Question</a>
      <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Question</th>
          <th>Correct Answer</th>
          <th>Difficulty</th>
          <th>Category</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($questions as $q): ?>
          <tr>
            <td><?= htmlspecialchars($q['id']) ?></td>
            <td><?= htmlspecialchars($q['question']) ?></td>
            <td><?= htmlspecialchars($q['correct_answer']) ?></td>
            <td><?= htmlspecialchars($q['difficulty']) ?></td>
            <td><?= htmlspecialchars($q['category']) ?></td>
            <td>
              <a href="edit_question.php?id=<?= $q['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="delete_question.php?id=<?= $q['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'footer.php'; ?>
