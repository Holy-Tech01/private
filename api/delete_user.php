<?php
require_once 'db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['admin_id'], $data['target_user_id'])) {
        echo json_encode(["success" => false, "error" => "Missing parameters"]);
        exit;
    }

    try {
        // Verify Admin Credentials
        $stmt = $pdo->prepare("SELECT matric_number FROM users WHERE id = ?");
        $stmt->execute([$data['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || $admin['matric_number'] !== '999999999') {
            echo json_encode(["success" => false, "error" => "Unauthorized. Only Super Admin can delete users."]);
            exit;
        }

        // Prevent deleting self
        if ($data['admin_id'] == $data['target_user_id']) {
            echo json_encode(["success" => false, "error" => "Cannot delete yourself."]);
            exit;
        }

        // Delete User (Cascading delete handles related data if foreign keys are set up correctly, but let's be safe)
        $pdo->beginTransaction();

        // Delete related records manually since ON DELETE CASCADE might not be set
        $pdo->prepare("DELETE FROM attendance WHERE user_id = ?")->execute([$data['target_user_id']]);
        $pdo->prepare("DELETE FROM registrations WHERE user_id = ?")->execute([$data['target_user_id']]);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$data['target_user_id']]);

        $pdo->commit();

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>