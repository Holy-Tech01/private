<?php
require_once 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (!isset($data['user_id']) || !isset($data['message_id']) || !isset($data['type'])) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit();
}

$user_id = $data['user_id'];
$message_id = $data['message_id'];
$type = $data['type'];

// Verify super-admin (matricNumber 999999999)
try {
    $stmt = $pdo->prepare("SELECT matric_number FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['matric_number'] !== '999999999') {
        echo json_encode(["success" => false, "error" => "Unauthorized access. Only super-admin can delete messages."]);
        exit();
    }

    if ($type === 'public') {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("DELETE FROM private_messages WHERE id = ?");
    }

    $stmt->execute([$message_id]);

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database error: " . $e->getMessage()]);
}
?>