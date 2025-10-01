<?php
// config.php
session_start();

$DB_HOST = 'localhost';
$DB_NAME = 'ilm_path';
$DB_USER = 'root';
$DB_PASS = ''; // set your DB password

$upload_base = __DIR__ . '/assets/uploads/';

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// helper to escape output
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
