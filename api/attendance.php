<?php
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    $training_day_id = $_GET['training_day_id'] ?? null;

    if ($user_id && !$training_day_id) {
        // Get all attendance for a user (for calendar)
        try {
            $stmt = $pdo->prepare("SELECT a.*, t.training_date FROM attendance a JOIN training_days t ON a.training_day_id = t.id WHERE a.user_id = ?");
            $stmt->execute([$user_id]);
            $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["success" => true, "attendance" => $attendance]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    } elseif ($training_day_id) {
        // Get all attendance for a training day (for admin verification)
        try {
            $stmt = $pdo->prepare("SELECT a.*, u.full_name, u.matric_number FROM attendance a JOIN users u ON a.user_id = u.id WHERE a.training_day_id = ?");
            $stmt->execute([$training_day_id]);
            $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["success" => true, "attendance" => $attendance]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    }
} elseif ($method === 'POST') {
    // User marks attendance
    $data = JSON_decode(file_get_contents("php://input"), true);
    if (!isset($data['user_id'], $data['training_day_id'])) {
        echo json_encode(["success" => false, "error" => "Missing data"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO attendance (user_id, training_day_id, status, verified) VALUES (?, ?, 'present', 0)");
        $stmt->execute([$data['user_id'], $data['training_day_id']]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} elseif ($method === 'PUT') {
    // Admin verifies attendance
    $data = JSON_decode(file_get_contents("php://input"), true);
    if (!isset($data['attendance_id'], $data['verified'])) {
        echo json_encode(["success" => false, "error" => "Missing data"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE attendance SET verified = ?, verified_at = ? WHERE id = ?");
        $stmt->execute([$data['verified'], $data['verified'] ? date('Y-m-d H:i:s') : null, $data['attendance_id']]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>