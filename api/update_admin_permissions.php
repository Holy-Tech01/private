<?php
require_once 'db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['admin_id'], $data['target_user_id'], $data['permissions'])) {
        echo json_encode(["success" => false, "error" => "Missing required parameters"]);
        exit;
    }

    try {
        // Verify Super Admin credentials
        $stmt = $pdo->prepare("SELECT matric_number FROM users WHERE id = ?");
        $stmt->execute([$data['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || $admin['matric_number'] !== '999999999') {
            echo json_encode(["success" => false, "error" => "Unauthorized. Only Super Admin can manage permissions."]);
            exit;
        }

        // Verify target user is an admin
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$data['target_user_id']]);
        $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$targetUser || $targetUser['role'] !== 'admin') {
            echo json_encode(["success" => false, "error" => "Target user is not an admin"]);
            exit;
        }

        $permissions = $data['permissions'];

        // Insert or update permissions
        $stmt = $pdo->prepare("
            INSERT INTO admin_permissions 
            (user_id, can_edit_users, can_delete_users, can_manage_admins, can_edit_messages, can_delete_messages)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                can_edit_users = VALUES(can_edit_users),
                can_delete_users = VALUES(can_delete_users),
                can_manage_admins = VALUES(can_manage_admins),
                can_edit_messages = VALUES(can_edit_messages),
                can_delete_messages = VALUES(can_delete_messages),
                updated_at = CURRENT_TIMESTAMP
        ");

        $stmt->execute([
            $data['target_user_id'],
            $permissions['can_edit_users'] ?? false,
            $permissions['can_delete_users'] ?? false,
            $permissions['can_manage_admins'] ?? false,
            $permissions['can_edit_messages'] ?? false,
            $permissions['can_delete_messages'] ?? false
        ]);

        echo json_encode(["success" => true, "message" => "Permissions updated successfully"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>