<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['question_file'])) {
  $file = $_FILES['question_file']['tmp_name'];
  $extension = strtolower(pathinfo($_FILES['question_file']['name'], PATHINFO_EXTENSION));

  if (!file_exists($file)) {
    $errors[] = "File upload failed. Please try again.";
  } elseif (!in_array($extension, ['csv', 'json'])) {
    $errors[] = "Only CSV or JSON files are supported.";
  } else {
    $inserted = 0;
    try {
      if ($extension === 'csv') {
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Skip header
        while (($data = fgetcsv($handle)) !== false) {
          if (count($data) >= 8) {
            $data = array_map('trim', $data);
            [$question, $a, $b, $c, $d, $correct, $category, $difficulty] = $data;

            $stmt = $pdo->prepare("INSERT INTO questions 
              (question, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$question, $a, $b, $c, $d, $correct, $category, $difficulty]);
            $inserted++;
          }
        }
        fclose($handle);
      } elseif ($extension === 'json') {
        $json = file_get_contents($file);
        $questions = json_decode($json, true);

        foreach ($questions as $q) {
          if (
            isset($q['question'], $q['option_a'], $q['option_b'], $q['option_c'], $q['option_d'],
                  $q['correct_option'], $q['category'], $q['difficulty'])
          ) {
            $stmt = $pdo->prepare("INSERT INTO questions 
              (question, option_a, option_b, option_c, option_d, correct_option, category, difficulty) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
              trim($q['question']), trim($q['option_a']), trim($q['option_b']),
              trim($q['option_c']), trim($q['option_d']), trim($q['correct_option']),
              trim($q['category']), trim($q['difficulty'])
            ]);
            $inserted++;
          }
        }
      }

      if ($inserted > 0) {
        $message = "$inserted questions uploaded successfully.";
      } else {
        $errors[] = "No valid questions found to insert.";
      }
    } catch (PDOException $e) {
      $errors[] = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
      $errors[] = "Unexpected error: " . $e->getMessage();
    }
  }
}

include '../header.php';
?>

<div class="container py-5">
  <h3 class="mb-4">📤 Upload Grammar Questions</h3>

  <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endforeach; ?>

  <form method="post" enctype="multipart/form-data" class="card shadow-sm p-4">
    <div class="mb-3">
      <label for="question_file" class="form-label">Select CSV or JSON File</label>
      <input class="form-control" type="file" name="question_file" id="question_file" accept=".csv,.json" required>
    </div>
    <button type="submit" class="btn btn-primary">📥 Upload Questions</button>
  </form>

  <div class="mt-5">
    <h6>🧾 Expected CSV Format:</h6>
    <pre class="bg-light p-2">question,option_a,option_b,option_c,option_d,correct_answer,category,difficulty</pre>
    <p class="text-muted">Correct answer must be one of the choices (e.g., "option_a").</p>
  </div>
</div>

<?php include '../footer.php'; ?>
