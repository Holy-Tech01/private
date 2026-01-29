<?php
/**
 * HOD SPORT DEPLOYMENT DEBUGGER
 * Upload this file to your website root and visit: http://geoscience.gamer.gd/debug.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><title>HOD Sport Debugger</title><style>
    body { font-family: sans-serif; line-height: 1.6; padding: 20px; background: #0f172a; color: #e2e8f0; }
    .card { background: #1e293b; padding: 20px; border-radius: 10px; border: 1px solid #334155; margin-bottom: 20px; }
    h2 { color: #38bdf8; margin-top: 0; }
    .success { color: #4ade80; font-weight: bold; }
    .error { color: #f87171; font-weight: bold; }
    .warning { color: #fbbf24; font-weight: bold; }
    pre { background: #000; padding: 10px; border-radius: 5px; overflow-x: auto; color: #fff; }
    table { width: 100%; border-collapse: collapse; }
    td, th { text-align: left; padding: 8px; border-bottom: 1px solid #334155; }
</style></head><body>";

echo "<h1>HOD Sport Deployment Debugger</h1>";

// 1. Environment Check
echo "<div class='card'><h2>1. Server Environment</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Root Path: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current File Path: " . __FILE__ . "<br>";
echo "</div>";

// 2. File Structure Verification
echo "<div class='card'><h2>2. File Structure Check</h2>";
$essential_files = [
    'index.html' => 'Frontend Entry (from dist)',
    'assets' => 'Frontend Assets (from dist)',
    'api/db.php' => 'Database Configuration',
    'api/auth.php' => 'Authentication API',
    'uploads' => 'Image Upload Directory'
];

echo "<table><tr><th>File/Folder</th><th>Expected Purpose</th><th>Status</th></tr>";
foreach ($essential_files as $file => $purpose) {
    echo "<tr><td>$file</td><td>$purpose</td><td>";
    if (file_exists($file)) {
        echo "<span class='success'>FOUND</span>";
    } else {
        echo "<span class='error'>MISSING</span>";
    }
    echo "</td></tr>";
}
echo "</table>";

if (file_exists('src')) {
    echo "<p class='warning'>⚠️ WARNING: The 'src' folder exists in the root. This is the source code. On a live server, you should only have the contents of the 'dist' folder.</p>";
}
echo "</div>";

// 3. Database Connection Check
echo "<div class='card'><h2>3. Database Connection</h2>";
if (file_exists('api/db.php')) {
    require_once 'api/db.php';
    if (isset($pdo)) {
        echo "<span class='success'>SUCCESS: Database connection established via api/db.php</span>";

        // Try to check key tables
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() > 0) {
                echo "<p class='success'>✓ 'users' table found.</p>";
            } else {
                echo "<p class='error'>✗ 'users' table MISSING from database!</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>Error querying database: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<span class='error'>FAILED: api/db.php loaded but PDO connection is not set.</span>";
    }
} else {
    echo "<span class='error'>FAILED: api/db.php not found. Cannot check database.</span>";
}
echo "</div>";

// 4. Directory Listing
echo "<div class='card'><h2>4. Root Directory Listing</h2><pre>";
$files = scandir('.');
foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        $type = is_dir($file) ? "[DIR] " : "      ";
        echo $type . $file . "\n";
    }
}
echo "</pre></div>";

echo "</body></html>";
?>