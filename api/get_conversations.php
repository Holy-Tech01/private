<?php
require_once 'db.php';

header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    die(json_encode(['success' => false, 'error' => 'User ID required']));
}

try {
    // Fetch all users except the current one
    // Include unread count for each user (messages sent BY them TO the current user that are unread)
    $sql = "SELECT u.id, u.full_name, u.recognition_name, u.role,
            (SELECT COUNT(*) FROM private_messages pm WHERE pm.sender_id = u.id AND pm.receiver_id = ? AND pm.is_read = 0) as unread_count
            FROM users u
            WHERE u.id != ? 
            ORDER BY unread_count DESC, u.full_name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $user_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'users' => $users]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>