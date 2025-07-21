<?php
require_once 'config/db.php';
$difficulty = $_POST['difficulty'] ?? '';
$category = $_POST['category'] ?? '';

$sql = "SELECT * FROM questions WHERE 1";
$params = [];

if ($difficulty && $difficulty !== 'all') {
    $sql .= " AND difficulty = ?";
    $params[] = $difficulty;
}
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
$sql .= " ORDER BY RAND() LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$questions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take Test</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3>Grammar Test (50 Questions)</h3>
  <form action="submit_test.php" method="post">
    <?php foreach ($questions as $i => $q): ?>
      <div class="card mb-3">
        <div class="card-body">
          <p><strong>Q<?= $i + 1 ?>:</strong> <?= htmlspecialchars($q['question']) ?></p>
          <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
            <div class="form-check">
              <input type="radio"
                     name="q<?= $q['id'] ?>"
                     value="option_<?= $opt ?>"
                     class="form-check-input"
                     id="q<?= $q['id'] ?>_<?= $opt ?>">
              <label class="form-check-label" for="q<?= $q['id'] ?>_<?= $opt ?>">
                <?= $q["option_$opt"] ?>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <button class="btn btn-success">✅ Submit Test</button>
  </form>
</div>
</body>
</html>
