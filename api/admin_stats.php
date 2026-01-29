<?php
require_once 'db.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(["error" => "User ID required for auth"]);
    exit();
}

$user_id = $_GET['user_id'];

// Verify Admin Role
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

try {
    // Fetch all registrations with user details
    $sql = "
        SELECT 
            r.*, 
            u.full_name, 
            u.matric_number, 
            u.recognition_name,
            u.email,
            u.phone
        FROM registrations r
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
    ";

    $stmt = $pdo->query($sql);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "registrations" => $registrations
    ]);

} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>