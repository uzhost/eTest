CREATE DATABASE IF NOT EXISTS grammar_test DEFAULT CHARACTER SET utf8mb4;
USE grammar_test;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question TEXT NOT NULL,
  option_a VARCHAR(255),
  option_b VARCHAR(255),
  option_c VARCHAR(255),
  option_d VARCHAR(255),
  correct_answer VARCHAR(255),
  difficulty ENUM('easy','medium','hard') DEFAULT 'easy',
  category VARCHAR(100)
);
