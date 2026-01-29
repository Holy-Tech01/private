<?php
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM training_days ORDER BY training_date DESC");
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "trainings" => $trainings]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    $data = JSON_decode(file_get_contents("php://input"), true);
    if (!isset($data['training_date'])) {
        echo json_encode(["success" => false, "error" => "Date is required"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO training_days (training_date, description) VALUES (?, ?)");
        $stmt->execute([$data['training_date'], $data['description'] ?? '']);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} elseif ($method === 'DELETE') {
    $training_id = $_GET['id'] ?? null;
    if (!$training_id) {
        echo json_encode(["success" => false, "error" => "ID is required"]);
        exit;
    }

    try {
        $pdo->beginTransaction();
        // Delete attendance records first
        $stmt = $pdo->prepare("DELETE FROM attendance WHERE training_day_id = ?");
        $stmt->execute([$training_id]);

        $stmt = $pdo->prepare("DELETE FROM training_days WHERE id = ?");
        $stmt->execute([$training_id]);
        $pdo->commit();
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>