<?php
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  try {
    $stmt->execute([$username, $email, $password]);
    header("Location: login.php");
  } catch (PDOException $e) {
    $error = "Username or email already exists.";
  }
}
?>

<!-- Registration form HTML with Bootstrap -->
