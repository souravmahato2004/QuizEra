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
    }
?>