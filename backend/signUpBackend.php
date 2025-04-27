<?php
session_start();
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $raw_password = $_POST['password'] ?? null;

    $_SESSION['pendingname'] = $name;
    $_SESSION['pendingemail'] = $email;

    if ($email && $raw_password) {
        $checkStmt = $conn->prepare("SELECT * FROM user_info WHERE user_email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('User with this email already exists. Please use another email or login.'); window.location.href='../frontend/signup.php';</script>";
        } else {
            // Password validation checks
            $errorMessages = [];

            if (strlen($raw_password) < 8) {
                $errorMessages[] = "Password must be at least 8 characters long.";
            }
            if (!preg_match('/\d/', $raw_password)) {
                $errorMessages[] = "Password must contain at least one number.";
            }
            if (!preg_match('/[\W_]/', $raw_password)) {
                $errorMessages[] = "Password must contain at least one special character.";
            }
            if (!preg_match('/[a-z]/', $raw_password)) {
                $errorMessages[] = "Password must contain at least one lowercase letter.";
            }
            if (!preg_match('/[A-Z]/', $raw_password)) {
                $errorMessages[] = "Password must contain at least one uppercase letter.";
            }

            // If there are any validation errors, show them
            if (!empty($errorMessages)) {
                echo "<script>alert('" . implode("\n", $errorMessages) . "'); window.location.href='../frontend/signup.php';</script>";
                exit;
            }

            // If password is valid, hash it and store in session
            $password = password_hash($raw_password, PASSWORD_DEFAULT);
            $_SESSION['pendingpassword'] = $password;

            // Redirect to OTP page
            // header('Location: ../frontend/signupOtp.php');
            include '../backend/otpgeneration.php';
            echo "<script>window.location.href='../frontend/signupOtp.php';</script>";
            exit;
        }

        $checkStmt->close();
    }
}
