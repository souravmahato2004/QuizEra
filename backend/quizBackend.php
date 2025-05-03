<?php 
    if(session_status()==PHP_SESSION_NONE){
        session_start();
    }
    include '../backend/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $user = $_SESSION['id'];
    
        $stmt = $conn->prepare("INSERT INTO quizzes (title, description, owner_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss",$title,$description,$user);
        $stmt->execute();
        
        $quizId = $db->lastInsertId();
        header("Location: " . $_SERVER['PHP_SELF'] . "?quiz=" . $quizId);
        $stmt->close();
        $conn->close();
        exit();
    }

    if(ctype_digit($_GET['quiz'])){
        $id=$_GET['quiz']??null;
        if($id== null){
            exit;
        }
        else{
            $checkstmt=$conn->prepare("SELECT title FROM quizzes WHERE quiz_id= ?");
            $checkstmt->bind_param('s',$id);
            $checkstmt->execute();
            $result=$checkstmt->get_result();
            if($result->num_rows>0){
                $row=$result->fetch_assoc();
                $title=$row['title'];
            }
            $checkstmt->close();
            $conn->close();
        }
    }
    else{
        $title=$_GET['quiz'];
    }

?>