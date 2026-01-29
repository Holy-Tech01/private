<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT cm.id, cm.message, cm.created_at, u.full_name, u.role, u.id as user_id 
            FROM community_messages cm 
            JOIN users u ON cm.user_id = u.id 
            ORDER BY cm.created_at ASC 
            LIMIT 100";

    $stmt = $pdo->query($sql);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>