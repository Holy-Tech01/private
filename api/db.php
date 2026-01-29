<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// DATABASE CONFIGURATION
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
    // LOCAL SETTINGS (XAMPP)
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'hod_sport'; // Make sure this matches your local database name
} else {
    // LIVE SERVER CREDENTIALS
    $host = 'sql109.infinityfree.com';
    $user = 'if0_40944758';
    $pass = 'n9mWgLC0koP3';
    $db = 'if0_40944758_geo_hod_sport';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit();
}
?>