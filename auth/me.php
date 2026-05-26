<?php
require __DIR__ . "/../config.php";

if (!isset($_SESSION['user_id'])) {
    apiSuccess("Not logged in", [
        "logged" => false
    ]);
}

$stmt = $pdo->prepare("
    SELECT email, role, gender
    FROM users
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    apiError("USER_NOT_FOUND", "User not found");
}

apiSuccess("User info", [
    "logged" => true,
    "email" => $user['email'],
    "role" => $user['role'],
    "gender" => $user['gender']
]);