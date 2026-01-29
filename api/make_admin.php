<?php
require_once 'db.php';

// The matric number you logged in with
$matricNumber = isset($_GET['matric']) ? $_GET['matric'] : '999999999';

try {
    // Update user to admin role
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);

    if ($stmt->rowCount() > 0) {
        // Verify the update
        $stmt = $pdo->prepare("SELECT id, full_name, matric_number, role FROM users WHERE matric_number = ?");
        $stmt->execute([$matricNumber]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<h2>✅ User Promoted to Admin!</h2>";
        echo "<p><strong>ID:</strong> {$user['id']}</p>";
        echo "<p><strong>Name:</strong> {$user['full_name']}</p>";
        echo "<p><strong>Matric:</strong> {$user['matric_number']}</p>";
        echo "<p><strong>Role:</strong> <span style='color: green; font-weight: bold;'>{$user['role']}</span></p>";
        echo "<hr>";
        echo "<p>Please <strong>log out</strong> and <strong>log back in</strong> for changes to take effect.</p>";
        echo "<p><a href='http://geosport.local/'>Go to Homepage</a></p>";
    } else {
        echo "<h2>❌ User Not Found</h2>";
        echo "<p>No user found with matric number: <strong>$matricNumber</strong></p>";
        echo "<p>Try: <a href='?matric=YOUR_MATRIC_NUMBER'>?matric=YOUR_MATRIC_NUMBER</a></p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>