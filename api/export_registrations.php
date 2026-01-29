<?php
require_once 'db.php';

// Set headers to force download as XLS (HTML Table compatible)
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=registrations_export_' . date('Y-m-d') . '.xls');

// Authentication Check
if (!isset($_GET['user_id'])) {
    die("Error: User ID required. Please append ?user_id=YOUR_ADMIN_ID to the URL.");
}

$user_id = $_GET['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    die("Error: Unauthorized. Admin access required.");
}

// Define the columns
$columns = [
    'football' => 'Football (Male & Female)',
    'basketball' => 'Basketball',
    'athletics' => 'Athletics',
    'volleyball' => 'Volleyball',
    'indoor' => 'Indoor Games'
];

try {
    // 1. Fetch registrations with user details and category
    $query = "
        SELECT 
            u.full_name,
            u.recognition_name,
            r.sport_id,
            r.category
        FROM registrations r
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Group users by sport
    $groupedData = [];
    foreach ($columns as $key => $label) {
        $groupedData[$key] = [];
    }

    foreach ($registrations as $row) {
        $sportId = $row['sport_id'];

        if (array_key_exists($sportId, $groupedData)) {
            // Format Name: SURNAME Firstname Middlename (Nickname) (Category)
            // Example: MUSTAPHA Damilol Olumide (Damilo1) or JUSTIN Pius-amo (Jay) (M 7)

            $fullName = $row['full_name'];
            $parts = explode(' ', trim($fullName));

            // First part is surname (make it UPPERCASE)
            $surname = strtoupper($parts[0]);

            // Rest is first/middle names (keep original case)
            $otherNames = implode(' ', array_slice($parts, 1));

            // Build formatted name
            $formattedName = htmlspecialchars($surname);
            if ($otherNames) {
                $formattedName .= ' ' . htmlspecialchars($otherNames);
            }

            // Add nickname in italics if present
            if (!empty($row['recognition_name'])) {
                $formattedName .= " <i>(" . htmlspecialchars($row['recognition_name']) . ")</i>";
            }

            // Add category inline for Athletics and Indoor (not on new line)
            if (($sportId === 'athletics' || $sportId === 'indoor') && !empty($row['category'])) {
                $formattedName .= " (" . htmlspecialchars($row['category']) . ")";
            }

            $groupedData[$sportId][] = $formattedName;
        }
    }

    // 3. Find max rows
    $maxRows = 0;
    foreach ($groupedData as $sportList) {
        $count = count($sportList);
        if ($count > $maxRows) {
            $maxRows = $count;
        }
    }

    // 4. Output HTML Table
    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<table border='1'>";

    // Header Row - Green background like reference
    echo "<tr style='background-color:#2d5016; color:white; font-weight:bold; text-align:center;'>";
    foreach ($columns as $label) {
        echo "<th style='padding:10px; border:1px solid #ccc;'>$label</th>";
    }
    echo "</tr>";

    // Data Rows
    for ($i = 0; $i < $maxRows; $i++) {
        echo "<tr>";
        foreach ($columns as $key => $label) {
            $cellContent = isset($groupedData[$key][$i]) ? $groupedData[$key][$i] : "";
            echo "<td style='padding:5px; vertical-align:top;'>$cellContent</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    echo "</body></html>";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>