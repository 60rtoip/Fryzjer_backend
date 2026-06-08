<?php

date_default_timezone_set('Europe/Warsaw');

header("Access-Control-Allow-Origin: *"); // na produkcji ogranicz do domeny frontend
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// ── Database ────────────────────────────────────────────────────────────────
$host     = "db";          // Docker service name (docker-compose)
$dbname   = "fryzjer";
$username = "fryzjer";
$password = "fryzjer_password";

// ── Application URL (no trailing slash) ──────────────────────────────────────
// Base URL of the FRONTEND app — used in email links.
// Email links will point to Angular routes (e.g. /verify?hash=...) so the
// user lands on the proper frontend page rather than a raw PHP response.
// In Docker this should be "http://localhost" (or your domain).
$app_url = "http://localhost";

// ── SMTP / Email ─────────────────────────────────────────────────────────────
$smtp_host      = "smtp.gmail.com";
$smtp_port      = 587;
$smtp_user      = "testmailpiotrw@gmail.com";
$smtp_pass      = "VHSADLZNGAZVELEJ"; // Gmail app password
$smtp_from_name = "Fryzjer";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log("DB ERROR: " . $e->getMessage());

    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "error" => "DB_CONNECTION_FAILED",
        "message" => "Database connection failed"
    ]);
    exit;
}

function apiSuccess(string $message, $data = null): void
{
    header("Content-Type: application/json");
    echo json_encode([
        "success" => true,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

function apiError(string $code, string $message): void
{
    error_log("API ERROR [$code]: $message");

    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "error" => $code,
        "message" => $message
    ]);
    exit;
}