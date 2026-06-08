<?php
require __DIR__ . "/../config.php";

$email = trim($_POST['email'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    apiError("INVALID_EMAIL", "Invalid email address");
}

/* Check if user exists */
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // security: do not reveal if email exists
    apiSuccess("If the email exists, a reset link has been sent");
}

/* Generate reset hash */
$resetHash = uniqid("reset_", true);

$expires = date('Y-m-d H:i:s', time() + 1800); // 30 minut

$stmt = $pdo->prepare("
    UPDATE users
    SET reset_hash = ?, reset_expires = ?
    WHERE email = ?
");
$stmt->execute([$resetHash, $expires, $email]);

require __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pass;
    $mail->SMTPSecure = "tls";
    $mail->Port = $smtp_port;

    $mail->setFrom($smtp_user, $smtp_from_name);
    $mail->addAddress($email);

    $link = $app_url . "/reset-password?hash=" . $resetHash;

    $mail->Subject = "Password reset";
    $mail->Body =
        "Click the link below to reset your password:\n\n" .
        $link;

    $mail->send();
} catch (Exception $e) {
    apiError("MAIL_ERROR", "Failed to send reset email");
}

apiSuccess("If the email exists, a reset link has been sent");