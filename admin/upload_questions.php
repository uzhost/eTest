<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['question_file'])) {
  $file = $_FILES['question_file']['tmp_name'];
  $extension = pathinfo($_FILES['question_file']['name'], PATHINFO_EXTENSION);

  if ($extension === 'csv') {
    $handle = fopen($file, 'r');
    fgetcsv($handle); // Skip header
    $inserted = 0;
    while (($data = fgetcsv($handle)) !== false) {
      if (count($data) >= 8) {
        [$question, $a, $b, $c, $d, $correct, $category, $difficulty] = $data;
        $stmt = $pdo->prepare("INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_option, category, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$question, $a, $b, $c, $d, $correct, $category, $difficulty]);
        $inserted++;
      }
    }
    fclose($handle);
    $message = "$inserted questions uploaded successfully.";
  } elseif ($extension === 'json') {
    $json = file_get_contents($file);
    $questions = json_decode($json, true);
    $inserted = 0;
    foreach ($questions as $q) {
      if (isset($q['question'], $q['option_a'], $q['option_b'], $q['option_c'], $q['option_d'], $q['correct_option'], $q['category'], $q['difficulty'])) {
        $stmt = $pdo->prepare("INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_option, category, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
          $q['question'], $q['option_a'], $q['option_b'], $q['option_c'], $q['option_d'],
          $q['correct_option'], $q['category'], $q['difficulty']
        ]);
        $inserted++;
      }
    }
    $message = "$inserted questions uploaded successfully.";
  } else {
    $message = "Only CSV or JSON files are allowed.";
  }
}

include 'header.php';
?>

<div class="container py-5">
  <h3 class="mb-4">📤 Upload Questions (CSV or JSON)</h3>
  <?php if ($message): ?>
    <div class="alert alert-info"> <?= htmlspecialchars($message) ?> </div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="card shadow-sm p-4">
    <div class="mb-3">
      <label for="question_file" class="form-label">Choose File</label>
      <input class="form-control" type="file" name="question_file" id="question_file" accept=".csv,.json" required>
    </div>
    <button type="submit" class="btn btn-primary">📥 Upload</button>
  </form>
  <div class="mt-4">
    <h6>🧾 CSV Format:</h6>
    <code>question,option_a,option_b,option_c,option_d,correct_option,category,difficulty</code>
    <br>
    <small>Correct option should be one of: option_a, option_b, option_c, option_d</small>
  </div>
</div>

<?php include 'footer.php'; ?>
