<?php
require_once 'db.php';

// Add email and phone columns to users table
try {
    // Check if columns already exist
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(255) DEFAULT NULL");
        echo "Added email column\n";
    } else {
        echo "Email column already exists\n";
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'phone'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(15) DEFAULT NULL");
        echo "Added phone column\n";
    } else {
        echo "Phone column already exists\n";
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL");
        echo "Added profile_picture column\n";
    } else {
        echo "Profile_picture column already exists\n";
    }

    echo "\nDatabase schema updated successfully!";
} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage();
}
?>