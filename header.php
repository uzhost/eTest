<!-- header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>eTest Platform</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to bottom, #f0f4f8, #e0eafc);
      font-family: 'Inter', sans-serif;
    }
    .card-style {
      background: #ffffff;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      margin-top: 60px;
      transition: transform 0.2s ease;
    }
    .card-style:hover {
      transform: translateY(-5px);
    }
    .btn-success {
      background-color: #28a745;
      border: none;
      transition: background-color 0.3s;
    }
    .btn-success:hover {
      background-color: #218838;
    }
    a {
      color: #007bff;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm animate__animated animate__fadeInDown">
  <div class="container">
    <a class="navbar-brand" href="/index.php">eTest</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#etestNavbar" aria-controls="etestNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="etestNavbar">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])):

//Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
        ?>
          <li class="nav-item">
            <a class="nav-link" href="/user/dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/user/results.php">Results</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/register.php">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
