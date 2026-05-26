<?php
require __DIR__ . "/../config.php";

if (!isset($_SESSION['reset_user_id'])) {
    apiError("INVALID_SESSION", "Reset session expired");
}

$password = trim($_POST['password'] ?? '');

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
    apiError("WEAK_PASSWORD", "Password must have 8 chars, upper and lower case");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    UPDATE users
    SET password_hash = ?, reset_hash = NULL
    WHERE id = ?
");
$stmt->execute([$hash, $_SESSION['reset_user_id']]);

unset($_SESSION['reset_user_id']);

apiSuccess("Password changed successfully");