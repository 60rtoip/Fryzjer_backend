<?php

//ini_set('display_errors', 0);
//error_reporting(E_ALL);

    
$host = "sql310.infinityfree.com";
$dbname = "if0_39855735_fryzjer";
$username = "if0_39855735";
$password = "Pitivv2007";


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
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "error" => "DB_CONNECTION_FAILED",
        "message" => "Database connection failed"
    ]);
    exit;
}


session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
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
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "error" => $code,
        "message" => $message
    ]);
    exit;
}


function visitDuration(string $gender): int
{
    if ($gender === 'male') {
        return 60;
    }

    if ($gender === 'female') {
        return 120;
    }

    return 0;
}