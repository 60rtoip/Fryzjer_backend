<?php
require __DIR__ . "/../config.php";

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$gender = $_POST['gender'] ?? null;

if (!$email || !$password || !in_array($gender, ['male','female'])) {
    apiError("INVALID_INPUT", "Invalid registration data");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    apiError("INVALID_EMAIL", "Invalid email format");
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
    apiError("WEAK_PASSWORD", "Password must have 8 chars, upper and lower case");
}

/* check if user exists */
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    apiError("EMAIL_EXISTS", "Account already exists");
}

/* create user */
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$verify_hash = uniqid("verify_", true);

$stmt = $pdo->prepare("
    INSERT INTO users (email, password_hash, gender, verify_hash, verified)
    VALUES (?, ?, ?, ?, 0)
");
$stmt->execute([$email, $password_hash, $gender, $verify_hash]);


require __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "testmailpiotrw@gmail.com";
    $mail->Password = "VHSADLZNGAZVELEJ"; // app password
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("testmailpiotrw@gmail.com", "Fryzjer");
    $mail->addAddress($email);

    $link = "https://60rtoip2.fast-page.org/Fryzjer_backend/verify.php?hash=" . $verify_hash;

    $mail->Subject = "Activate your account";
    $mail->Body = 
        "Thank you for registration.\n\n".
        "Click the link below to activate your account:\n".
        $link;

    $mail->send();
} catch (Exception $e) {
    apiError("MAIL_ERROR", "Email sending failed");
}

apiSuccess("Registration successful. Check your email to verify account.");