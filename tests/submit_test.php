<?php
require_once '../config/db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

include '../header.php';

$user_id = $_SESSION['user_id'];

// Validate POST presence
if (empty($_POST)) {
    include '../footer.php';
    exit('<div class="container mt-5"><div class="alert alert-warning">No answers submitted. <a href="start_test.php">Go back</a>.</div></div>');
}

// Collect submitted question IDs and answers
$submittedQuestionIds = [];
$userAnswers = []; // qid => answer (e.g. option_a)
foreach ($_POST as $key => $value) {
    if (preg_match('/^q(\d+)$/', $key, $matches)) {
        $qid = (int)$matches[1];
        $submittedQuestionIds[] = $qid;
        $userAnswers[$qid] = $value;
    }
}

// If nothing valid — show friendly message
if (empty($submittedQuestionIds)) {
    ?>
    <div class="container mt-5">
      <div class="alert alert-warning border-start border-4 border-warning-subtle shadow-sm p-4 text-center">
        <h4 class="mb-2">⚠️ No Answers Detected</h4>
        <p class="mb-3">We couldn't detect any answers in your submission. Make sure you selected answers and clicked Submit.</p>
        <div class="d-flex justify-content-center gap-2">
          <a href="start_test.php" class="btn btn-primary">🔁 Retake Test</a>
          <a href="../user/dashboard.php" class="btn btn-outline-secondary">🏠 Dashboard</a>
        </div>
      </div>
    </div>
    <?php
    include '../footer.php';
    exit;
}

// Fetch full question rows for the submitted IDs
$placeholders = implode(',', array_fill(0, count($submittedQuestionIds), '?'));
$stmt = $pdo->prepare("SELECT id, question, option_a, option_b, option_c, option_d, correct_answer FROM questions WHERE id IN ($placeholders)");
$stmt->execute($submittedQuestionIds);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build associative map by id (preserve original order by $submittedQuestionIds)
$questionsMap = [];
foreach ($rows as $r) {
    $questionsMap[$r['id']] = $r;
}

// Score calculation
$score = 0;
$correctAnswers = 0;
$details = []; // for display in same order as submittedQuestionIds

foreach ($submittedQuestionIds as $qid) {
    $q = $questionsMap[$qid] ?? null;
    $userAnswer = $userAnswers[$qid] ?? null; // e.g. "option_a"
    $correct = $q ? $q['correct_answer'] : null;

    $isCorrect = ($userAnswer && $correct && $userAnswer === $correct);
    if ($isCorrect) {
        $score += 2; // per your scoring rule
        $correctAnswers++;
    }

    $details[] = [
        'id' => $qid,
        'question' => $q ? $q['question'] : '[Question not found]',
        'options' => $q ? [
            'option_a' => $q['option_a'],
            'option_b' => $q['option_b'],
            'option_c' => $q['option_c'],
            'option_d' => $q['option_d'],
        ] : [],
        'user_answer' => $userAnswer,
        'correct_answer' => $correct,
        'is_correct' => $isCorrect
    ];
}

$total = count($details);

// Save overall result into DB
try {
    $stmt = $pdo->prepare("INSERT INTO results (user_id, score, total_questions, correct_answers, date_taken) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $score, $total, $correctAnswers]);
    $result_id = $pdo->lastInsertId();
} catch (PDOException $e) {
    // show friendly error
    ?>
    <div class="container mt-5">
      <div class="alert alert-danger">
        <h4>❌ Could not save results</h4>
        <p>There was a server error saving your result. Please try again later.</p>
        <div class="small text-muted">Error: <?= htmlspecialchars($e->getMessage()) ?></div>
        <a href="start_test.php" class="btn btn-primary mt-2">Retry</a>
      </div>
    </div>
    <?php
    include '../footer.php';
    exit;
}

// Clear session test data
unset($_SESSION['questions'], $_SESSION['test_filters'], $_SESSION['answers']);
?>

<!-- RESULT UI -->
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
          <h2 class="mb-1">🎉 Test Completed</h2>
          <p class="text-muted mb-3">Well done — here is your summary.</p>

          <div class="row">
            <div class="col-md-4 mb-3">
              <div class="p-3 border rounded">
                <small class="text-muted">Score</small>
                <div class="h3 fw-bold"><?= htmlspecialchars($score) ?> / <?= htmlspecialchars($total * 2) ?></div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="p-3 border rounded">
                <small class="text-muted">Correct Answers</small>
                <div class="h3 fw-bold"><?= htmlspecialchars($correctAnswers) ?> / <?= htmlspecialchars($total) ?></div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <?php
                $percent = $total ? round(($score / ($total * 2)) * 100) : 0;
              ?>
              <div class="p-3 border rounded">
                <small class="text-muted">Percentage</small>
                <div class="h3 fw-bold"><?= $percent ?>%</div>
                <div class="progress mt-2" style="height:10px;">
                  <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex justify-content-center gap-2 flex-wrap">
            <a href="start_test.php" class="btn btn-primary">📘 Take Another Test</a>
            <a href="../user/dashboard.php" class="btn btn-outline-secondary">🏠 Dashboard</a>
            <a href="javascript:void(0)" id="exportCsvBtn" class="btn btn-outline-info">⬇ Export CSV</a>
            <a href="review_results.php?id=<?= urlencode($result_id) ?>" class="btn btn-outline-success">🔎 Review Answers</a>
          </div>
        </div>
      </div>

      <!-- Question-by-question review -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">🔍 Review — Question by question</h5>

          <?php foreach ($details as $i => $d): ?>
            <div class="mb-3 p-3 rounded <?= $d['is_correct'] ? 'border border-success bg-success bg-opacity-10' : 'border border-danger bg-danger bg-opacity-10' ?>">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <strong>Q<?= $i + 1 ?>:</strong> <?= htmlspecialchars($d['question']) ?>
                </div>
                <div class="text-end small text-muted"><?= $d['is_correct'] ? 'Correct' : 'Incorrect' ?></div>
              </div>

              <div class="row">
                <?php foreach ($d['options'] as $optKey => $optVal): 
                  if ($optVal === null || $optVal === '') continue;
                  $isUser = ($d['user_answer'] === $optKey);
                  $isCorrect = ($d['correct_answer'] === $optKey);
                  $badgeClass = $isCorrect ? 'badge bg-success' : ($isUser ? 'badge bg-secondary' : ''); 
                ?>
                  <div class="col-md-6 mb-2">
                    <div class="p-2 rounded <?= $isCorrect ? 'border border-success' : ($isUser ? 'border border-secondary' : 'border border-transparent') ?>">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <strong><?= strtoupper(str_replace('option_', '', $optKey)) ?>.</strong> <?= htmlspecialchars($optVal) ?>
                        </div>
                        <div>
                          <?php if ($isCorrect): ?>
                            <span class="badge bg-success">✔ Correct</span>
                          <?php elseif ($isUser): ?>
                            <span class="badge bg-warning text-dark">✖ Your answer</span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

            </div>
          <?php endforeach; ?>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
  // Export the result + answers as CSV
  document.getElementById('exportCsvBtn').addEventListener('click', function () {
    const rows = [];
    rows.push(['Question #','Question','Your Answer','Correct Answer','Is Correct','Score']);

    <?php
    // Prepare JS-safe JSON of details
    $jsDetails = json_encode($details, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
    echo "const details = $jsDetails;\n";
    ?>

    for (let i=0;i<details.length;i++) {
      const d = details[i];
      const qnum = i+1;
      const qtext = d.question.replace(/\n/g, ' ');
      const user = d.user_answer ? d.user_answer : '';
      const correct = d.correct_answer ? d.correct_answer : '';
      const isCorrect = d.is_correct ? 'YES' : 'NO';
      const scored = d.is_correct ? 2 : 0;
      rows.push([qnum, qtext, user, correct, isCorrect, scored]);
    }

    let csv = rows.map(r => r.map(cell => `"${String(cell).replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'etest_result_<?= htmlspecialchars($result_id) ?>.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  });
</script>

<?php include '../footer.php'; ?>
