<?php
require_once 'db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $userId = $_GET['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(["success" => false, "error" => "Missing user_id parameter"]);
        exit;
    }

    try {
        // Check if user is an admin
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || $user['role'] !== 'admin') {
            echo json_encode([
                "success" => true,
                "permissions" => [
                    "can_edit_users" => false,
                    "can_delete_users" => false,
                    "can_manage_admins" => false,
                    "can_edit_messages" => false,
                    "can_delete_messages" => false
                ]
            ]);
            exit;
        }

        // Get permissions from database
        $stmt = $pdo->prepare("SELECT * FROM admin_permissions WHERE user_id = ?");
        $stmt->execute([$userId]);
        $permissions = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$permissions) {
            // No permissions set, return defaults (all false)
            echo json_encode([
                "success" => true,
                "permissions" => [
                    "can_edit_users" => false,
                    "can_delete_users" => false,
                    "can_manage_admins" => false,
                    "can_edit_messages" => false,
                    "can_delete_messages" => false
                ]
            ]);
        } else {
            echo json_encode([
                "success" => true,
                "permissions" => [
                    "can_edit_users" => (bool) $permissions['can_edit_users'],
                    "can_delete_users" => (bool) $permissions['can_delete_users'],
                    "can_manage_admins" => (bool) $permissions['can_manage_admins'],
                    "can_edit_messages" => (bool) $permissions['can_edit_messages'],
                    "can_delete_messages" => (bool) $permissions['can_delete_messages']
                ]
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>