<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->user_id) || !isset($data->registration_id) || !isset($data->status)) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

// Verify admin user
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$data->user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    echo json_encode(["error" => "Unauthorized: Admin access required"]);
    exit();
}

try {
    // Update registration status
    $stmt = $pdo->prepare("UPDATE registrations SET status = ? WHERE id = ?");
    $stmt->execute([$data->status, $data->registration_id]);

    echo json_encode([
        "success" => true,
        "message" => "Registration status updated successfully"
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>