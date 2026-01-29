<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    // 1. Create private_messages table
    $sql1 = "CREATE TABLE IF NOT EXISTS private_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql1);

    // 2. Create user_last_read table (for unread notifications badge)
    $sql2 = "CREATE TABLE IF NOT EXISTS user_last_read (
        user_id INT NOT NULL,
        feature VARCHAR(50) NOT NULL, -- 'community' or 'private'
        last_read_id INT DEFAULT 0,
        PRIMARY KEY (user_id, feature),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql2);

    echo json_encode(['success' => true, 'message' => 'Private messaging tables created successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>