<?php
require_once 'db.php';

try {
    // Create training_days table
    $pdo->exec("CREATE TABLE IF NOT EXISTS training_days (
        id INT AUTO_INCREMENT PRIMARY KEY,
        training_date DATE UNIQUE NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'training_days' verified/created.\n";

    // Create attendance table
    $pdo->exec("CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        training_day_id INT NOT NULL,
        status ENUM('present') DEFAULT 'present',
        verified TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        verified_at TIMESTAMP NULL DEFAULT NULL,
        UNIQUE KEY user_training (user_id, training_day_id)
    )");
    echo "Table 'attendance' verified/created.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
?>