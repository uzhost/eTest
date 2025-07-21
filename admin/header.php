<?php
session_start();
if (!isset($_SESSION['admin_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - eTest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .navbar {
      background-color: #343a40;
    }
    .navbar .nav-link, .navbar-brand {
      color: #fff;
    }
    .navbar .nav-link:hover {
      color: #ffc107;
    }
    .container {
      margin-top: 30px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">🛠️ Admin Panel</a>
    <?php if (isset($_SESSION['admin_id'])): ?>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="questions.php">❓ Questions</a></li>
        <li class="nav-item"><a class="nav-link" href="add_questions.php">➕ Add</a></li>
        <li class="nav-item"><a class="nav-link" href="results.php">📊 Results</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">🚪 Logout</a></li>
      </ul>
    <?php endif; ?>
  </div>
</nav>

<div class="container">
