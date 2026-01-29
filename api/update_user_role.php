<?php
require_once 'db.php';

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$target_user_id = $input['target_user_id'] ?? null;
$new_role = $input['new_role'] ?? null;
$admin_id = $input['admin_id'] ?? null;

if (!$target_user_id || !$new_role || !$admin_id) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    // 1. Verify the requester is the SUPER ADMIN (999999999)
    $stmt = $pdo->prepare("SELECT matric_number, role FROM users WHERE id = ?");
    $stmt->execute([$admin_id]);
    $requester = $stmt->fetch();

    if (!$requester || $requester['matric_number'] !== '999999999') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized: Only the Super Admin (999999999) can change roles.']);
        exit;
    }

    // 2. Prevent the super admin from demoting themselves (optional but recommended)
    $stmt = $pdo->prepare("SELECT matric_number FROM users WHERE id = ?");
    $stmt->execute([$target_user_id]);
    $target = $stmt->fetch();

    if ($target && $target['matric_number'] === '999999999' && $new_role !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Cannot demote the Super Admin account.']);
        exit;
    }

    // 3. Update the role
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $target_user_id]);

    echo json_encode(['success' => true, 'message' => "User role updated to $new_role successfully"]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>