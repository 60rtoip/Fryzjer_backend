<?php
require "config.php";

$hash = $_GET['hash'] ?? null;

if (!$hash) {
    echo "Invalid verification link";
    exit;
}

$stmt = $pdo->prepare("
    SELECT id FROM users
    WHERE verify_hash = ? AND verified = 0
");
$stmt->execute([$hash]);
$user = $stmt->fetch();

if (!$user) {
    echo "Link invalid or account already verified";
    exit;
}

$stmt = $pdo->prepare("
    UPDATE users
    SET verified = 1, verify_hash = NULL
    WHERE id = ?
");
$stmt->execute([$user['id']]);

echo "Account activated. Redirecting...";
header("Location: index.php");
