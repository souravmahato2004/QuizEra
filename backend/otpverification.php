<?php

if (isset($_POST['submitOtp'])) {
    // session_start();  // Start the session
    
    if (!isset($_SESSION['pendingemail']) || !isset($_SESSION['pendingname'])) {
        echo "<script>alert('Session expired. Please register again.'); window.location.href='../frontend/signup.php';</script>";
        exit;
    }
    
    include 'db.php';
    
    // Clean expired OTPs (optional but recommended)
    if (!$conn->query("DELETE FROM user_otps WHERE expires_at < NOW()")) {
        echo "<script>alert('Error cleaning expired OTPs: " . $conn->error . "');</script>";
        exit;
    }
    
    $email = $_SESSION['pendingemail'] ?? null;
    $otp = $_POST['otp'] ?? null;
    
    if ($email && $otp) {
        // Check if OTP exists and is still valid
        $checkOtpStmt = $conn->prepare("SELECT * FROM user_otps WHERE email = ? AND otp = ? AND expires_at > NOW()");
        $checkOtpStmt->bind_param("ss", $email, $otp);
        $checkOtpStmt->execute();
        $result = $checkOtpStmt->get_result();
    
        if ($result->num_rows > 0) {
            // OTP is valid - fetch details from session
            $name = $_SESSION['pendingname'] ?? null;
            $password = $_SESSION['pendingpassword'] ?? null;
    
            if ($name && $password) {
                $stmt = $conn->prepare("INSERT INTO user_info (username, user_email, user_password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $password);
    
                if ($stmt->execute()) {
                    // Clean up OTPs for this email
                    $deleteOtpStmt = $conn->prepare("DELETE FROM user_otps WHERE email = ?");
                    $deleteOtpStmt->bind_param("s", $email);
                    $deleteOtpStmt->execute();
                    $deleteOtpStmt->close();
    
                    // Clear session values
                    unset($_SESSION['pendingname']);
                    unset($_SESSION['pendingemail']);
                    unset($_SESSION['pendingpassword']);
    
                    // Redirect to the signup page after successful insertion
                    header("Location: ../frontend/signup.php?status=success");
                    exit;
                } else {
                    // If there's an error during signup, show the error message
                    echo "<script>alert('Error during signup: " . $stmt->error . "'); window.location.href='../frontend/signup.php';</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Session expired or missing details. Please try again.'); window.location.href='../frontend/signup.php';</script>";
            }
        } else {
            echo "<script>alert('Invalid or expired OTP. Please try again.'); window.location.href='../frontend/signup.php';</script>";
        }
        $checkOtpStmt->close();
    } else {
        echo "<script>alert('Missing email or OTP.'); window.location.href='../frontend/signup.php';</script>";
    }    
}
?>
