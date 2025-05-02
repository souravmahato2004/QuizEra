<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizId = $_POST['quizId'];
    $quizName = trim($_POST['quizName']);
    $quizDescription = trim($_POST['quizDescription'] ?? '');
    
    // Validate inputs
    if (empty($quizName)) {
        echo json_encode(['success' => false, 'message' => 'Quiz name is required']);
        exit();
    }
    
    // Check if user owns this quiz
    $stmt = $conn->prepare("SELECT owner_id FROM quizzes WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $quiz = $result->fetch_assoc();
    
    if (!$quiz || $quiz['owner_id'] != $_SESSION['id']) {
        echo json_encode(['success' => false, 'message' => 'You are not authorized to edit this quiz']);
        exit();
    }
    
    // Handle file upload if provided
    $newFileName = null;
    if (isset($_FILES['quizImage']) && $_FILES['quizImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../assets/quizImage/";
        $fileExt = pathinfo($_FILES['quizImage']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExt;
        $targetFile = $targetDir . $newFileName;
        
        if (move_uploaded_file($_FILES['quizImage']['tmp_name'], $targetFile)) {
            // Delete old image if it exists
            $stmt = $conn->prepare("SELECT image_url FROM quizzes WHERE quiz_id = ?");
            $stmt->bind_param("i", $quizId);
            $stmt->execute();
            $result = $stmt->get_result();
            $oldImage = $result->fetch_assoc()['image_url'];
            
            if ($oldImage && file_exists($targetDir . $oldImage)) {
                unlink($targetDir . $oldImage);
            }
        } else {
            $newFileName = null;
        }
    }
    
    // Update database
    if ($newFileName) {
        $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ?, image_url = ?, updated_at = NOW() WHERE quiz_id = ?");
        $stmt->bind_param("sssi", $quizName, $quizDescription, $newFileName, $quizId);
    } else {
        $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ?, updated_at = NOW() WHERE quiz_id = ?");
        $stmt->bind_param("ssi", $quizName, $quizDescription, $quizId);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
    $stmt->close();
    $conn->close();
    header("Location: ../frontend/mainPage.php");
}
?>