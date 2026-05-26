<?php
require __DIR__ . "/../config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    apiError("FORBIDDEN", "Admin access required");
}

$date = $_POST['date'] ?? null;
if (!$date) {
    apiError("INVALID_INPUT", "Missing date");
}

$stmt = $pdo->prepare("
    INSERT INTO days_off (date)
    VALUES (?)
    ON DUPLICATE KEY UPDATE date = date
");
$stmt->execute([$date]);

$stmt = $pdo->prepare("
    DELETE FROM reservations
    WHERE date = ?
");
$stmt->execute([$date]);
``
apiSuccess("Day off added");