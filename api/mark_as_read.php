<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;
$feature = $data['feature'] ?? 'community';
$last_read_id = $data['last_read_id'] ?? 0;

if (!$user_id) {
    die(json_encode(['success' => false, 'error' => 'User ID required']));
}

try {
    $sql = "INSERT INTO user_last_read (user_id, feature, last_read_id) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE last_read_id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $feature, $last_read_id, $last_read_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>