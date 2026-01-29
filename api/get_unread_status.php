<?php
require_once 'db.php';

header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    die(json_encode(['success' => false, 'error' => 'User ID required']));
}

try {
    // 1. Check Private Unread
    $sqlPrivate = "SELECT COUNT(*) as unread_count FROM private_messages WHERE receiver_id = ? AND is_read = 0";
    $stmtPrivate = $pdo->prepare($sqlPrivate);
    $stmtPrivate->execute([$user_id]);
    $privateUnread = $stmtPrivate->fetch(PDO::FETCH_ASSOC)['unread_count'];

    // 2. Check Community Unread
    // Get last read ID from user_last_read
    $sqlLastRead = "SELECT last_read_id FROM user_last_read WHERE user_id = ? AND feature = 'community'";
    $stmtLastRead = $pdo->prepare($sqlLastRead);
    $stmtLastRead->execute([$user_id]);
    $lastReadId = $stmtLastRead->fetch(PDO::FETCH_ASSOC)['last_read_id'] ?? 0;

    // Count messages later than lastReadId
    $sqlComm = "SELECT COUNT(*) as unread_count FROM community_messages WHERE id > ? AND user_id != ?";
    $stmtComm = $pdo->prepare($sqlComm);
    $stmtComm->execute([$lastReadId, $user_id]);
    $commUnread = $stmtComm->fetch(PDO::FETCH_ASSOC)['unread_count'];

    echo json_encode([
        'success' => true,
        'unread' => [
            'private' => (int) $privateUnread,
            'community' => (int) $commUnread,
            'total' => (int) $privateUnread + (int) $commUnread
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>