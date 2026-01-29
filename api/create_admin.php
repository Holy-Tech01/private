<?php
require_once 'db.php';

$matricNumber = '999999999';
$password = 'admin123';
$fullName = 'System Administrator';
$recognitionName = 'Admin';
$role = 'admin';

// Check if user already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE matric_number = ?");
$stmt->execute([$matricNumber]);
$existing = $stmt->fetch();

if ($existing) {
    // Update existing user to be admin (in case they lost it or it's a reset)
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin', password_hash = ? WHERE matric_number = ?");
    $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $matricNumber]);
    echo json_encode(["success" => true, "message" => "Admin user updated. Login with Matric: $matricNumber, Pass: $password"]);
} else {
    // Create new admin user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, recognition_name, matric_number, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $recognitionName, $matricNumber, password_hash($password, PASSWORD_DEFAULT), $role]);
        echo json_encode(["success" => true, "message" => "Admin user created. Login with Matric: $matricNumber, Pass: $password"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to create admin: " . $e->getMessage()]);
    }
}
?>