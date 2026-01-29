<?php
require_once 'db.php';

try {
    // Tables creation logic (using the existing $pdo from db.php)
    echo "Connected to Database.<br>";

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
id INT AUTO_INCREMENT PRIMARY KEY,
full_name VARCHAR(255) NOT NULL,
recognition_name VARCHAR(100) NOT NULL,
matric_number VARCHAR(20) NOT NULL UNIQUE,
email VARCHAR(255) DEFAULT NULL,
phone VARCHAR(15) DEFAULT NULL,
profile_picture VARCHAR(255) DEFAULT NULL,
password_hash VARCHAR(255) NOT NULL,
role ENUM('student', 'admin') DEFAULT 'student',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
    echo "Table 'users' created or exists.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS registrations (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
sport_id VARCHAR(50) NOT NULL,
level VARCHAR(10),
position VARCHAR(50),
shirt_number VARCHAR(10),
role VARCHAR(50),
category VARCHAR(50),
status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id)
)");
    echo "Table 'registrations' created or exists.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS training_days (
id INT AUTO_INCREMENT PRIMARY KEY,
training_date DATE UNIQUE NOT NULL,
description TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
    echo "Table 'training_days' created or exists.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS attendance (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
training_day_id INT NOT NULL,
status ENUM('present') DEFAULT 'present',
verified TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
verified_at TIMESTAMP NULL DEFAULT NULL,
UNIQUE KEY user_training (user_id, training_day_id),
FOREIGN KEY (user_id) REFERENCES users(id),
FOREIGN KEY (training_day_id) REFERENCES training_days(id) ON DELETE CASCADE
)");
    echo "Table 'attendance' created or exists.\n";

    echo "Setup completed successfully.\n";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage() . "\n");
}
?>