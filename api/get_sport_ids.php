<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect without DB first
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected. Listing databases:\n";
    $stmt = $pdo->query("SHOW DATABASES");
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($dbs as $d) {
        echo "- $d\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>