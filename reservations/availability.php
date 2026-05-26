<?php
require __DIR__ . "/../config.php";

$date = $_GET['date'] ?? null;
if (!$date) {
    apiError("INVALID_INPUT", "Missing date");
}

/* DAY OFF */
$stmt = $pdo->prepare("SELECT 1 FROM days_off WHERE date = ?");
$stmt->execute([$date]);

if ($stmt->fetch()) {
    apiSuccess("Day off", [
        "day_off" => true,
        "slots" => []
    ]);
}

$open = new DateTime("$date 09:00");
$close = new DateTime("$date 18:00");
$interval = new DateInterval("PT30M");

$stmt = $pdo->prepare("SELECT hour, duration FROM reservations WHERE date = ?");
$stmt->execute([$date]);
$reservations = $stmt->fetchAll();

$busy = [];

foreach ($reservations as $r) {
    $start = new DateTime("$date ".$r['hour']);
    $end = (clone $start)->modify("+{$r['duration']} minutes");
    $busy[] = [$start, $end];
}

$slots = [];

for ($t = clone $open; $t < $close; $t->add($interval)) {
    $status = "free";

    foreach ($busy as [$bs, $be]) {
        if ($t >= $bs && $t < $be) {
            $status = "busy";
            break;
        }
    }

    $slots[] = [
        "hour" => $t->format("H:i"),
        "status" => $status
    ];
}

apiSuccess("Availability fetched", [
    "day_off" => false,
    "slots" => $slots
]);