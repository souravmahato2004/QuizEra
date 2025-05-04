<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['id'];

// Process AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_quiz':
            createQuiz($conn);
            break;
        case 'update_quiz':
            updateQuiz($conn);
            break;
        case 'load_quiz':
            loadQuiz($conn);
            break;
        case 'save_slides':
            saveSlides($conn);
            break;
        case 'add_collaborator':
            addCollaborator($conn);
            break;
        default:
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
    exit;
}

// Get quiz details for the editor
if (isset($_GET['quiz'])) {
    $quiz_id = $_GET['quiz'];
    
    // Check if numeric quiz ID
    if (ctype_digit($quiz_id)) {
        // Load quiz data
        $stmt = $conn->prepare("SELECT title, description FROM quizzes WHERE quiz_id = ?");
        $stmt->bind_param('i', $quiz_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $quiz = $result->fetch_assoc();
            $title = $quiz['title'];
            $description = $quiz['description'];
            
            // Check permission
            $permissionStmt = $conn->prepare("
                SELECT 1 FROM quizzes WHERE quiz_id = ? AND owner_id = ?
                UNION
                SELECT 1 FROM quiz_collaborators WHERE quiz_id = ? AND user_id = ? AND permission_level = 'edit'
            ");
            $permissionStmt->bind_param('iiii', $quiz_id, $user_id, $quiz_id, $user_id);
            $permissionStmt->execute();
            $permResult = $permissionStmt->get_result();
            
            $canEdit = $permResult->num_rows > 0;
            $permissionStmt->close();
            
            if (!$canEdit) {
                // Redirect to view mode or show error
                header("Location: ../pages/accessDenied.php");
                exit;
            }
        } else {
            // Quiz not found
            header("Location: ../pages/notFound.php");
            exit;
        }
        $stmt->close();
    } else {
        // Quiz ID is not numeric, assuming it's a title for a new quiz
        $title = $_GET['quiz'];
        $description = "";
    }
}

// Functions to handle quiz operations
function createQuiz($conn) {
    $user_id = $_SESSION['id'];
    $title = $_POST['title'] ?? 'Untitled Quiz';
    $description = $_POST['description'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO quizzes (title, description, owner_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $description, $user_id);
    
    header('Content-Type: application/json');
    if ($stmt->execute()) {
        $quiz_id = $conn->insert_id;
        echo json_encode(['status' => 'success', 'quiz_id' => $quiz_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    $stmt->close();
}

function updateQuiz($conn) {
    $quiz_id = $_POST['quiz_id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $is_published = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;
    $user_id = $_SESSION['id'];
    
    // Check permission
    $permStmt = $conn->prepare("
        SELECT 1 FROM quizzes WHERE quiz_id = ? AND owner_id = ?
        UNION
        SELECT 1 FROM quiz_collaborators WHERE quiz_id = ? AND user_id = ? AND permission_level = 'edit'
    ");
    $permStmt->bind_param('iiii', $quiz_id, $user_id, $quiz_id, $user_id);
    $permStmt->execute();
    
    header('Content-Type: application/json');
    if ($permStmt->get_result()->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Permission denied']);
        $permStmt->close();
        return;
    }
    $permStmt->close();
    
    $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ?, is_published = ? WHERE quiz_id = ?");
    $stmt->bind_param("ssii", $title, $description, $is_published, $quiz_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    $stmt->close();
}

function loadQuiz($conn) {
    $quiz_id = $_POST['quiz_id'] ?? 0;
    $user_id = $_SESSION['id'];
    
    // Check permission (can view or edit)
    $permStmt = $conn->prepare("
        SELECT 1 FROM quizzes WHERE quiz_id = ? AND owner_id = ?
        UNION
        SELECT 1 FROM quiz_collaborators WHERE quiz_id = ? AND user_id = ?
    ");
    $permStmt->bind_param('iiii', $quiz_id, $user_id, $quiz_id, $user_id);
    $permStmt->execute();
    
    header('Content-Type: application/json');
    if ($permStmt->get_result()->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Permission denied']);
        $permStmt->close();
        return;
    }
    $permStmt->close();
    
    // Get quiz data
    $stmt = $conn->prepare("
        SELECT q.title, q.description, q.is_published 
        FROM quizzes q
        WHERE q.quiz_id = ?
    ");
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();
    $quizResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Get slides
    $slideStmt = $conn->prepare("
        SELECT slide_id, question, question_type, position, image_url
        FROM quiz_slides
        WHERE quiz_id = ?
        ORDER BY position ASC
    ");
    $slideStmt->bind_param('i', $quiz_id);
    $slideStmt->execute();
    $slidesResult = $slideStmt->get_result();
    $slideStmt->close();
    
    $slides = [];
    while ($slide = $slidesResult->fetch_assoc()) {
        $slide_id = $slide['slide_id'];
        
        // Get options for multiple choice questions
        if ($slide['question_type'] === 'multiple') {
            $optStmt = $conn->prepare("
                SELECT option_text, is_correct, position
                FROM slide_options
                WHERE slide_id = ?
                ORDER BY position ASC
            ");
            $optStmt->bind_param('i', $slide_id);
            $optStmt->execute();
            $options = [];
            $correctAnswer = null;
            $optResult = $optStmt->get_result();
            
            while ($opt = $optResult->fetch_assoc()) {
                $options[] = $opt['option_text'];
                if ($opt['is_correct']) {
                    $correctAnswer = $opt['position']; 
                }
            }
            $optStmt->close();
            
            $slide['options'] = $options;
            $slide['correctAnswer'] = $correctAnswer;
        } 
        // Get answers for other question types
        else {
            $ansStmt = $conn->prepare("
                SELECT correct_answer FROM slide_answers
                WHERE slide_id = ?
            ");
            $ansStmt->bind_param('i', $slide_id);
            $ansStmt->execute();
            $ansResult = $ansStmt->get_result();
            
            if ($ans = $ansResult->fetch_assoc()) {
                $slide['options'] = [$ans['correct_answer']];
            } else {
                $slide['options'] = [''];
            }
            $slide['correctAnswer'] = 0;
            $ansStmt->close();
        }
        
        $slides[] = $slide;
    }
    
    // Get collaborators
    $collabStmt = $conn->prepare("
        SELECT c.user_id, u.username, c.permission_level
        FROM quiz_collaborators c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.quiz_id = ?
    ");
    $collabStmt->bind_param('i', $quiz_id);
    $collabStmt->execute();
    $collabResult = $collabStmt->get_result();
    $collaborators = [];
    
    while ($collab = $collabResult->fetch_assoc()) {
        $collaborators[] = $collab;
    }
    $collabStmt->close();
    
    // Get owner info
    $ownerStmt = $conn->prepare("
        SELECT u.username
        FROM quizzes q
        JOIN users u ON q.owner_id = u.user_id
        WHERE q.quiz_id = ?
    ");
    $ownerStmt->bind_param('i', $quiz_id);
    $ownerStmt->execute();
    $ownerResult = $ownerStmt->get_result()->fetch_assoc();
    $ownerStmt->close();
    
    echo json_encode([
        'status' => 'success',
        'quiz' => $quizResult,
        'slides' => $slides,
        'owner' => $ownerResult,
        'collaborators' => $collaborators
    ]);
}

function saveSlides($conn) {
    $quiz_id = $_POST['quiz_id'] ?? 0;
    $slides = json_decode($_POST['slides'] ?? '[]', true);
    $user_id = $_SESSION['id'];
    
    // Check permission
    $permStmt = $conn->prepare("
        SELECT 1 FROM quizzes WHERE quiz_id = ? AND owner_id = ?
        UNION
        SELECT 1 FROM quiz_collaborators WHERE quiz_id = ? AND user_id = ? AND permission_level = 'edit'
    ");
    $permStmt->bind_param('iiii', $quiz_id, $user_id, $quiz_id, $user_id);
    $permStmt->execute();
    
    header('Content-Type: application/json');
    if ($permStmt->get_result()->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Permission denied']);
        $permStmt->close();
        return;
    }
    $permStmt->close();
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // First, delete existing slides for this quiz
        $deleteStmt = $conn->prepare("DELETE FROM quiz_slides WHERE quiz_id = ?");
        $deleteStmt->bind_param('i', $quiz_id);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Insert new slides
        $slideStmt = $conn->prepare("
            INSERT INTO quiz_slides (quiz_id, question, question_type, position, image_url)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $optionStmt = $conn->prepare("
            INSERT INTO slide_options (slide_id, option_text, is_correct, position)
            VALUES (?, ?, ?, ?)
        ");
        
        $answerStmt = $conn->prepare("
            INSERT INTO slide_answers (slide_id, correct_answer)
            VALUES (?, ?)
        ");
        
        foreach ($slides as $position => $slide) {
            $question = $slide['question'];
            $type = $slide['type'];
            $image = $slide['image'] ?? null;
            
            $slideStmt->bind_param('issis', $quiz_id, $question, $type, $position, $image);
            $slideStmt->execute();
            $slide_id = $conn->insert_id;
            
            if ($type === 'multiple') {
                foreach ($slide['options'] as $idx => $option) {
                    $is_correct = ($idx == $slide['correctAnswer']) ? 1 : 0;
                    $optionStmt->bind_param('isii', $slide_id, $option, $is_correct, $idx);
                    $optionStmt->execute();
                }
            } else {
                // For fill-in-blank and short answer
                $answer = $slide['options'][0] ?? '';
                $answerStmt->bind_param('is', $slide_id, $answer);
                $answerStmt->execute();
            }
        }
        
        $slideStmt->close();
        $optionStmt->close();
        $answerStmt->close();
        
        // Commit transaction
        $conn->commit();
        echo json_encode(['status' => 'success']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function addCollaborator($conn) {
    $quiz_id = $_POST['quiz_id'] ?? 0;
    $email = $_POST['email'] ?? '';
    $permission = $_POST['permission'] ?? 'view';
    $user_id = $_SESSION['id'];
    
    // Check if user is owner
    $ownerStmt = $conn->prepare("SELECT 1 FROM quizzes WHERE quiz_id = ? AND owner_id = ?");
    $ownerStmt->bind_param('ii', $quiz_id, $user_id);
    $ownerStmt->execute();
    
    header('Content-Type: application/json');
    if ($ownerStmt->get_result()->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Only the owner can add collaborators']);
        $ownerStmt->close();
        return;
    }
    $ownerStmt->close();
    
    // Find user by email
    $userStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $userStmt->bind_param('s', $email);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    if ($userResult->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        $userStmt->close();
        return;
    }
    
    $collaborator_id = $userResult->fetch_assoc()['user_id'];
    $userStmt->close();
    
    // Add collaborator
    $stmt = $conn->prepare("
        INSERT INTO quiz_collaborators (quiz_id, user_id, permission_level, invited_by)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE permission_level = ?
    ");
    $stmt->bind_param('iisss', $quiz_id, $collaborator_id, $permission, $user_id, $permission);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    $stmt->close();
}
?>