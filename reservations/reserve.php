<?php

require __DIR__ . "/../config.php";
require __DIR__ . "/../services.php";

if (!isset($_SESSION['user_id'])) {
    apiError("UNAUTHORIZED", "User not logged in");
}

$date = trim($_POST['date'] ?? '');
$hour = trim($_POST['hour'] ?? '');
$service = $_POST['service'] ?? null;

if (!$date || !$hour || !$service) {
    apiError("INVALID_INPUT", "Missing data");
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    apiError("INVALID_DATE", "Invalid date format");
}

if (!preg_match('/^\d{2}:\d{2}/', $hour)) {
    apiError("INVALID_HOUR", "Invalid hour format");
}

$hour = substr($hour, 0, 5);

$minute = (int) substr($hour, 3, 2);
if (!in_array($minute, [0, 30])) {
    apiError("INVALID_SLOT", "Invalid time slot");
}

/*  DAY OFF  */
$stmt = $pdo->prepare("SELECT 1 FROM days_off WHERE date = ?");
$stmt->execute([$date]);
if ($stmt->fetch()) {
    apiError("DAY_OFF", "Day off");
}

$stmt = $pdo->prepare("
    SELECT gender, verified
    FROM users
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    apiError("USER_NOT_FOUND", "User not found");
}

if (!$user['verified']) {
    apiError("NOT_VERIFIED", "Account not verified");
}

/*  SERVICE  */
try {
    $duration = getServiceDuration($user['gender'], $service);
} catch (Exception $e) {
    apiError("INVALID_SERVICE", "Service not allowed");
}

$start = new DateTime("$date $hour");
$end   = (clone $start)->modify("+{$duration} minutes");
$now   = new DateTime();

/*  PAST  */
if ($start < $now) {
    apiError("PAST_TIME", "Cannot reserve in the past");
}

/*  MINIMUM  */
$minAdvance = new DateTime("+1 hour");
if ($start < $minAdvance) {
    apiError("TOO_SOON", "Reservation must be made at least 1 hour in advance");
}

/*  WORKING HOURS  */
$open  = new DateTime("$date 09:00");
$close = new DateTime("$date 18:00");

if ($start < $open || $end > $close) {
    apiError("OUTSIDE_WORKING_HOURS", "Outside working hours");
}

/*  LIMIT PER DAY  */
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM reservations
    WHERE user_id = ? AND date = ?
");
$stmt->execute([$_SESSION['user_id'], $date]);

if ($stmt->fetchColumn() >= 3) {
    apiError("LIMIT_EXCEEDED", "Maximum 3 reservations per day");
}

/*  COLLISION CHECK  */
$stmt = $pdo->prepare("
    SELECT id FROM reservations
    WHERE date = ?
    AND TIME(hour) < ?
    AND ADDTIME(hour, SEC_TO_TIME(duration*60)) > ?
");
$stmt->execute([
    $date,
    $end->format("H:i"),
    $hour
]);

if ($stmt->rowCount() > 0) {
    apiError("BUSY", "Slot busy");
}

$stmt = $pdo->prepare("
    INSERT INTO reservations (user_id, date, hour, duration, service_type)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $_SESSION['user_id'],
    $date,
    $hour,
    $duration,
    $service
]);

apiSuccess("Reservation added");