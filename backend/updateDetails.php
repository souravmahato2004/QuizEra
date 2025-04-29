<?php
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
            $imageData=null;
            if (isset($_FILES['user_pic']) && $_FILES['user_pic']['error'] === UPLOAD_ERR_OK) {
                $imageTmpPath = $_FILES['user_pic']['tmp_name'];
                $imageData = file_get_contents($imageTmpPath);
            }
            $_SESSION['name']=$Name;
            $_SESSION['username']=$username;
            $_SESSION['profilepic']=$imageData;

            if ($imageData !== null) {
                $stmt = $conn->prepare("UPDATE user_info SET name=?, username=?, user_pic=? WHERE id=?");
                $stmt->bind_param("ssbs", $Name, $username, $imageData, $id);
                $stmt->send_long_data(2, $imageData);
            } else {
                $stmt = $conn->prepare("UPDATE user_info SET name=?, username=? WHERE user_id=?");
                $stmt->bind_param("ssi", $Name, $username, $id);
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