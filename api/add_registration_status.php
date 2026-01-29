<?php
require_once 'db.php';

// Add status column to registrations table for approval system
try {
    // Check if status column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM registrations LIKE 'status'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE registrations ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER category");
        echo "Added status column to registrations table\n";
    } else {
        echo "Status column already exists\n";
    }

    echo "\nRegistrations table updated successfully!";
} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage();
}
?>