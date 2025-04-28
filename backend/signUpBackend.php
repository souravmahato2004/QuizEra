<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['form_name']) && $_POST['form_name'] == 'signupForm'){
        session_start();
        include 'db.php';
        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $raw_password = trim($_POST['password']) ?? null;
    
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
    
                // If there are any validation errors
                if (!empty($errorMessages)) {
                    echo "<script>alert('" . implode("\n", $errorMessages) . "'); window.location.href='../frontend/signup.php';</script>";
                    exit;
                }
    
                // If password is valid, hash it and store in session
                $password = password_hash($raw_password, PASSWORD_DEFAULT);
                $_SESSION['pendingpassword'] = $password;
    
                // Redirect to OTP page
                include '../backend/otpgeneration.php';
                echo "<script>window.location.href='../frontend/signupOtp.php';</script>";
                exit;
            }
            $_POST['form_name']=null;
    
            $checkStmt->close();
        }
    }
    else if(isset($_POST['form_name']) && $_POST['form_name'] == 'signinForm'){
        $email = $_POST['email'] ?? null;
        $loginpassword = $_POST['loginpassword'] ?? null;

        if ($email && $loginpassword) {
            include 'db.php';
            $checkStmt = $conn->prepare("SELECT * FROM user_info WHERE user_email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
    
            if ($result->num_rows > 0) {
                $row=$result->fetch_assoc();
                if(password_verify($loginpassword,$row['user_password'])){
                    if(session_status()==PHP_SESSION_NONE){
                        session_start();
                    }                    $_SESSION['email']=$email;
                    $_SESSION['id']=$row['user_id'];
                    echo "<script>window.location.href='../frontend/mainPage.php';</script>";
                    exit;
                }
                else{
                    echo "<script>alert('Password incorrect'); window.location.href='../frontend/signup.php';</script>";
                }
            }
            else{
                echo "<script>alert('User not found'); window.location.href='../frontend/signup.php';</script>";
            }
            $checkStmt->close();
        }
    }

}
