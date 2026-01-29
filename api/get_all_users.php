<?php
require_once 'db.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(["error" => "User ID required"]);
    exit();
}

$user_id = $_GET['user_id'];

// Verify admin user
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    echo json_encode(["error" => "Unauthorized: Admin access required"]);
    exit();
}

try {
    // Fetch all users with their registration count
    $stmt = $pdo->query("
        SELECT 
            u.id,
            u.full_name,
            u.recognition_name,
            u.email,
            u.phone,
            u.matric_number,
            u.role,
            u.profile_picture,
            COUNT(r.id) as registration_count
        FROM users u
        LEFT JOIN registrations r ON u.id = r.user_id
        GROUP BY u.id
        ORDER BY u.id DESC
    ");

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format profile pictures
    foreach ($users as &$user) {
        if ($user['profile_picture'] && !str_starts_with($user['profile_picture'], 'http')) {
            $user['profile_picture'] = 'http://localhost/HOD%20Sport/' . $user['profile_picture'];
        }
    }

    echo json_encode([
        "success" => true,
        "users" => $users
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>