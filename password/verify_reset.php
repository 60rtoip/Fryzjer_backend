<?php
require __DIR__ . "/../config.php";

$hash = $_GET['hash'] ?? null;

if (!$hash) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT id FROM users
    WHERE reset_hash = ?
    AND reset_expires > NOW()
");
$stmt->execute([$hash]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: index.php");
    exit;
}

$_SESSION['reset_user_id'] = $user['id'];

header("Location: ../index.php");
exit;