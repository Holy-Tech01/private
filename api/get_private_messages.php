<?php
require_once 'db.php';

header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? null;
$other_id = $_GET['other_id'] ?? null;

if (!$user_id || !$other_id) {
    die(json_encode(['success' => false, 'error' => 'IDs required']));
}

try {
    $sql = "SELECT * FROM private_messages 
            WHERE (sender_id = ? AND receiver_id = ?) 
               OR (sender_id = ? AND receiver_id = ?) 
            ORDER BY created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $other_id, $other_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark as read
    $updateSql = "UPDATE private_messages SET is_read = 1 
                  WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$other_id, $user_id]);

    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>