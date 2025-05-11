<?php
include '../backend/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$profilePic = null;

if (isset($_SESSION['username'])) {
    $stmt = $conn->prepare("SELECT user_pic FROM user_info WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $profilePic = $row['user_pic'];
    }

    $stmt->close();
}

$conn->close();

    if($_SERVER['REQUEST_METHOD']=='POST'){
        if(isset($_POST['username']) && $_POST['form_name']=='nameSection'){
            if(session_status()==PHP_SESSION_NONE){
                session_start();
            }
            include 'db.php';
            $checkstmt=$conn->prepare("SELECT * FROM user_info WHERE username=?");
            $checkstmt->bind_param("s",$_SESSION['username']);
            $checkstmt->execute();
            $result=$checkstmt->get_result();
            $id=0;
            if($result->num_rows>0){
                $row=$result->fetch_assoc();
                $id=$row['user_id'];
            }
            $Name = $_POST['Name'] ?? null;
            $username = $_POST['username'] ?? null;
            $quizImagePath = null;
            $newFileName=null;
            if (isset($_FILES['user_pic']) && $_FILES['user_pic']['error'] === UPLOAD_ERR_OK) {
                $targetDir = "../assets/profilepic/";
                $fileExt = pathinfo($_FILES['user_pic']['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid() . '.' . $fileExt;
                $targetFile = $targetDir . $newFileName;

                // Validate and move uploaded file
                if (move_uploaded_file($_FILES['user_pic']['tmp_name'], $targetFile)) {
                    $quizImagePath = $targetFile;
                }
            }
            $_SESSION['name']=$Name;
            $_SESSION['username']=$username;
            $_SESSION['profilepic']=$newFileName;

            if ($quizImagePath !== null) {
                $stmt = $conn->prepare("UPDATE user_info SET name=?, username=?, user_pic=? WHERE user_id=?");
                $stmt->bind_param("sssi", $Name, $username, $newFileName, $id);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("UPDATE user_info SET name=?, username=?, user_pic=? WHERE user_id=?");
                $stmt->bind_param("sssi", $Name, $username,$newFileName, $id);
            }
            $stmt->execute();
            $checkstmt->close();
            $stmt->close();
            $conn->close();
            header("Location: ../frontend/accountSettings.php");
            exit();
        }
    }
?>