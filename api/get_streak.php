<?php
require_once 'db.php';

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["success" => false, "error" => "User ID required"]);
    exit;
}

try {
    // 1. Get all training days sorted by date DESC
    $stmt = $pdo->query("SELECT id, training_date FROM training_days ORDER BY training_date DESC");
    $training_days = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Get verified attendance for this user
    $stmt = $pdo->prepare("SELECT training_day_id FROM attendance WHERE user_id = ? AND verified = 1");
    $stmt->execute([$user_id]);
    $attended_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Ensure all IDs are treated as strings for consistent comparison
    $attended_ids = array_map('strval', $attended_ids);

    $streak = 0;
    $today = date('Y-m-d');
    $found_any_past_training = false;

    foreach ($training_days as $day) {
        if ($day['training_date'] > $today)
            continue; // Skip future trainings

        if (in_array((string) $day['id'], $attended_ids)) {
            $streak++;
            $found_any_past_training = true;
        } else {
            // If the latest training day is TODAY and the user hasn't been verified yet,
            // we don't break the streak immediately. We check the previous training day.
            if ($day['training_date'] === $today) {
                continue;
            }

            // If they missed a training day in the past, the streak is broken
            break;
        }
    }

    echo json_encode(["success" => true, "streak" => $streak]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}