<?php
    include '../backend/db.php';
    if(session_status()==PHP_SESSION_NONE){
        session_start();
    }
    if(!isset($_SESSION['email'])){
        header("Location: ../frontend/signup.php");
        exit();
    }

    // Process form data
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['newQuiz']) && $_POST['newQuiz']==='createNewQuiz')) {
        // Validate and sanitize inputs
        $quizName = trim($_POST['quizName']);
        $quizDescription = trim($_POST['quizDescription'] ?? '');
        
        // Basic validation
        if (empty($quizName)) {
            $_SESSION['error'] = "Quiz name is required";
            header("Location: ../frontend/mainPage.php"); // Redirect back to form
            exit();
        }
        
        // Sanitize the quiz name for URL
        $cleanQuizName = preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($quizName));
        $cleanQuizName = preg_replace('/-+/', '-', $cleanQuizName);
        $cleanQuizName = trim($cleanQuizName, '-');
        
        // Handle file upload if provided
        $quizImagePath = null;
        if (isset($_FILES['quizImage']) && $_FILES['quizImage']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../assets/quizImage/";
            $fileExt = pathinfo($_FILES['quizImage']['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExt;
            $targetFile = $targetDir . $newFileName;
            
            // Validate and move uploaded file
            if (move_uploaded_file($_FILES['quizImage']['tmp_name'], $targetFile)) {
                $quizImagePath = $targetFile;
            }
        }
        
        // Here you would typically save to database
        // For now we'll just redirect
        $stmt = $conn->prepare("INSERT INTO quizzes (title, description,owner_id, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss",$quizName,$quizDescription,$_SESSION['id'],$newFileName);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Redirect to the new quiz page
        header("Location: quiz.php?quiz=" . urlencode($cleanQuizName));
        exit();
    }
?>

<?php
    // Get current user
    // Fetch all quizzes owned by or shared with the current user
    $stmt = $conn->prepare("
        SELECT q.title, q.description, q.image_url, q.quiz_id, q.updated_at, q.is_published, 
            u.username as owner_name, u.user_id as owner_id
        FROM quizzes q
        JOIN user_info u ON q.owner_id = u.user_id 
        WHERE q.owner_id = ? OR q.quiz_id IN (
            SELECT quiz_id FROM quiz_collaborators WHERE user_id = ?
        )
        ORDER BY q.updated_at DESC
    ");
    $user_id = $_SESSION['id'];
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $quizzes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();

?>