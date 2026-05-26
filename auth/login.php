<?php
require __DIR__ . "/../config.php";

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    apiError("INVALID_INPUT", "Missing login data");
}

$stmt = $pdo->prepare("SELECT id, password_hash, role, verified FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    apiError("INVALID_CREDENTIALS", "Invalid email or password");
}if (!$user['verified']) {
    apiError("NOT_VERIFIED", "Account not verified. Check your email.");
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

apiSuccess("Logged in successfully", [
    "role" => $user['role']
]);