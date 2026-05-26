<?php
require __DIR__ . "/../config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];

session_destroy();

apiSuccess("Logged out successfully");