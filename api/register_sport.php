<?php
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->user_id) || !isset($data->sport_id)) {
    echo json_encode(["error" => "Invalid input"]);
    exit();
}

$user_id = $data->user_id;
$sport_id = $data->sport_id;
$level = $data->level ?? '';
$position = $data->position ?? '';
$shirt_number = $data->shirt_number ?? '';
$role = $data->role ?? ''; // Player, Coach, etc.
$category = $data->category ?? '';

// Check if already registered for THIS sport
// Check if already registered
if ($sport_id === 'athletics' || $sport_id === 'indoor') {
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND sport_id = ? AND category = ?");
    $stmt->execute([$user_id, $sport_id, $category]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "You are already registered for this race ($category)."]);
        exit();
    }
} else {
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND sport_id = ?");
    $stmt->execute([$user_id, $sport_id]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "You are already registered for this sport."]);
        exit();
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO registrations (user_id, sport_id, level, position, shirt_number, role, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $sport_id, $level, $position, $shirt_number, $role, $category]);

    echo json_encode(["success" => true, "message" => "Registration successful"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>