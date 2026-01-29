<?php
require_once 'db.php';

echo "<h2>All Users in Database</h2>";

try {
    $stmt = $pdo->query("SELECT id, full_name, matric_number, role FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) == 0) {
        echo "<p><strong>No users found in database.</strong></p>";
        echo "<p>Please register an account first at <a href='http://geosport.local/'>the homepage</a></p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Matric</th><th>Role</th><th>Action</th></tr>";

        foreach ($users as $user) {
            $color = $user['role'] === 'admin' ? 'green' : 'black';
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['full_name']}</td>";
            echo "<td>{$user['matric_number']}</td>";
            echo "<td style='color: $color; font-weight: bold;'>{$user['role']}</td>";
            echo "<td>";
            if ($user['role'] !== 'admin') {
                echo "<a href='make_admin.php?matric={$user['matric_number']}'>Make Admin</a>";
            } else {
                echo "✓ Already Admin";
            }
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>