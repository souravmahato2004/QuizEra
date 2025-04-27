<?php
ob_start();
session_start();
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? null;
    $_SESSION['pendingname'] = $name;
    $email = $_POST['email'] ?? null;
    $_SESSION['pendingemail'] = $email;
    $raw_password = $_POST['password'] ?? null;

    if ($email && $raw_password) {
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('User with this email already exists. Please use another email or login.'); window.location.href='../frontend/signup.php';</script>";
        } else {
            if (strlen($raw_password) < 8) {
                echo "Password must be at least 8 characters long.";
                exit;
            }
            if (!preg_match('/\d/', $raw_password)) {
                echo "Password must contain at least one number.";
                exit;
            }
            if (!preg_match('/[\W_]/', $raw_password)) {
                echo "Password must contain at least one special character.";
                exit;
            }
            if (!preg_match('/[a-z]/', $raw_password)) {
                echo "Password must contain at least one lowercase letter.";
                exit;
            }
            if (!preg_match('/[A-Z]/', $raw_password)) {
                echo "Password must contain at least one uppercase letter.";
                exit;
            }

            // passed all checks
            $password = password_hash($raw_password, PASSWORD_DEFAULT);
            $_SESSION['pendingpassword'] = $password;
            echo "<script>alert('User with this email already exists. Please use another email or login.');</script>";
            include('../backend/otpgeneration.php');
            echo "<script>showOtpModal()</script>";
            exit;
        }
        $checkStmt->close();
    }
}
?>
