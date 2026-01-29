<?php
require_once 'db.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(["error" => "User ID required"]);
    exit();
}

$user_id = $_GET['user_id'];

try {
    // Fetch all registrations including status
    $stmt = $pdo->prepare("SELECT * FROM registrations WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create a simple array of sport_ids for easy checking
    $registeredSports = array_column($registrations, 'sport_id');

    echo json_encode([
        "registeredSports" => $registeredSports,
        "registrations" => $registrations
    ]);

} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>