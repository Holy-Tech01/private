<?php
require_once 'db.php';

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('student', 'admin') DEFAULT 'student'");
        echo "Column 'role' added successfully.\n";
    } else {
        echo "Column 'role' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
?>