<?php
require_once 'db.php';

header('Content-Type: application/json');

$admin_id = $_GET['user_id'] ?? null;

if (!$admin_id) {
    die(json_encode(['success' => false, 'error' => 'Admin ID required']));
}

try {
    // Verify admin role
    $checkSql = "SELECT role FROM users WHERE id = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$admin_id]);
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['role'] !== 'admin') {
        die(json_encode(['success' => false, 'error' => 'Unauthorized Access']));
    }

    // List all unique pairs that have chatted
    $sql = "SELECT DISTINCT 
                LEAST(sender_id, receiver_id) as user1, 
                GREATEST(sender_id, receiver_id) as user2,
                (SELECT full_name FROM users WHERE id = LEAST(sender_id, receiver_id)) as name1,
                (SELECT full_name FROM users WHERE id = GREATEST(sender_id, receiver_id)) as name2,
                MAX(created_at) as last_msg
            FROM private_messages 
            GROUP BY user1, user2
            ORDER BY last_msg DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $threads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'threads' => $threads]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>