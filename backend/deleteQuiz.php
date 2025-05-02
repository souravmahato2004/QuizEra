<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizId = $_POST['quizId'];
    
    // Check if user owns this quiz
    $stmt = $conn->prepare("SELECT owner_id, image_url FROM quizzes WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $quiz = $result->fetch_assoc();
    
    if (!$quiz || $quiz['owner_id'] != $_SESSION['id']) {
        echo json_encode(['success' => false, 'message' => 'You are not authorized to delete this quiz']);
        exit();
    }
    
    // Delete image file if it exists
    if ($quiz['image_url']) {
        $targetDir = "../assets/quizImage/";
        if (file_exists($targetDir . $quiz['image_url'])) {
            unlink($targetDir . $quiz['image_url']);
        }
    }
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM quizzes WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
    $stmt->close();
    $conn->close();
}
?>