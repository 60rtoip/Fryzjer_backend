<?php
require __DIR__ . "/../config.php";

if (!isset($_SESSION['user_id'])) {
    apiError("UNAUTHORIZED", "User not logged in");
}

$isAdmin = ($_SESSION['role'] === 'admin');

if ($isAdmin) {
    $stmt = $pdo->query("
        SELECT r.id, r.date, r.hour, r.duration, r.service_type, u.email
        FROM reservations r
        JOIN users u ON u.id = r.user_id
        ORDER BY r.date, r.hour
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT r.id, r.date, r.hour, r.duration, r.service_type, u.email
        FROM reservations r
        JOIN users u ON u.id = r.user_id
        WHERE r.user_id = ?
        ORDER BY r.date, r.hour
    ");
    $stmt->execute([$_SESSION['user_id']]);
}

apiSuccess("OK", $stmt->fetchAll());