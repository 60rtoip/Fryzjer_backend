<?php
require __DIR__ . "/../config.php";

if (!isset($_SESSION['user_id'])) {
    apiError("UNAUTHORIZED", "User not logged in");
}

$reservationId = $_POST['reservation_id'] ?? null;

if (!$reservationId || !is_numeric($reservationId)) {
    apiError("INVALID_INPUT", "Missing or invalid reservation id");
}

$stmt = $pdo->prepare("
    SELECT user_id FROM reservations WHERE id = ?
");
$stmt->execute([$reservationId]);
$reservation = $stmt->fetch();

if (!$reservation) {
    apiError("NOT_FOUND", "Reservation not found");
}

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

if (!$isAdmin && $reservation['user_id'] != $_SESSION['user_id']) {
    apiError("FORBIDDEN", "You cannot cancel this reservation");
}

$stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
$stmt->execute([$reservationId]);

apiSuccess("Reservation cancelled");