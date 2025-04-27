<?php
session_start();
include 'db.php';

echo"<script>alert('User with this email already exists. Please use another email or login.');</script>";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// check session data
if (!isset($_SESSION['pendingemail']) || !isset($_SESSION['pendingname']) || !isset($_SESSION['pendingpassword'])) {
    echo "Session data missing. Please register again.";
    exit;
}

$email = $_SESSION['pendingemail'];
$name = $_SESSION['pendingname'];
$password = $_SESSION['pendingpassword'];

// Generate OTP
$otp = rand(100000, 999999);
$expiresAt = date('Y-m-d H:i:s', time() + 120); // 2 minutes

// Store OTP
$stmt = $conn->prepare("INSERT INTO user_otps (email, otp, expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $otp, $expiresAt);
$stmt->execute();
$stmt->close();

require '../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'quizera2025@gmail.com';
    $mail->Password = 'tmnbvkncmysiwnpd';  // Must be App Password, not real password!
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('quizera2025@gmail.com', 'Quizera Team');
    $mail->addReplyTo('quizera2025@gmail.com', 'Quizera Support');
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code for Quizera (' . date('Y-m-d') . ')';
    $mail->Body = '
        <div style="font-family: Arial, sans-serif; padding: 20px;">
            <h2>Hello ' . htmlspecialchars($name) . ',</h2>
            <p>Your OTP is:</p>
            <h1 style="color:blue;">' . $otp . '</h1>
            <p>Expires in 2 minutes.</p>
        </div>';
    $mail->AltBody = 'Hello ' . $name . ', Your OTP is: ' . $otp . ' (Expires in 2 minutes)';

    $mail->send();

    // Redirect to OTP Verification Page
    // header("Location: ../frontend/verifyotp.php");
    exit;
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
