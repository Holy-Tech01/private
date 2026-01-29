<?php
/**
 * SURVIVAL DEBUGGER
 * No dependencies, no complex logic.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Status Check</h1>";
echo "<ul>";
echo "<li>PHP is working: <strong>YES</strong></li>";
echo "<li>Current Time: " . date('Y-m-d H:i:s') . "</li>";
echo "<li>Asset Check (JS): " . (file_exists('assets') ? "Folder exists" : "Folder MISSING") . "</li>";
echo "<li>Database Check (db.php): " . (file_exists('api/db.php') ? "File exists" : "api/db.php MISSING") . "</li>";
echo "</ul>";

echo "<h2>Root Directory Files:</h2><pre>";
print_r(scandir('.'));
echo "</pre>";

if (isset($_GET['check_db'])) {
    echo "<h2>Testing DB Connection...</h2>";
    try {
        include 'api/db.php';
        echo "PDO object: " . (isset($pdo) ? "Created successfully" : "NOT FOUND in db.php");
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>