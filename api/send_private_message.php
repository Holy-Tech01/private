<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

$data = json_decode(file_get_contents('php://input'), true);
$sender_id = $data['sender_id'] ?? null;
$receiver_id = $data['receiver_id'] ?? null;
$message = $data['message'] ?? null;

if (!$sender_id || !$receiver_id || !$message) {
    die(json_encode(['success' => false, 'error' => 'Missing fields']));
}

try {
    $sql = "INSERT INTO private_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sender_id, $receiver_id, $message]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>