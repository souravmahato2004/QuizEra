<?php
session_start();
include '../backend/db.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

// Get quiz information
$quiz_id = isset($_GET['id']) ? $_GET['id'] : null;
$title = $quiz_id ? getQuizTitle($quiz_id, $conn) : 'Untitled Quiz';

// Check if this is a participant joining via code
$is_participant = isset($_GET['join_code']);
$session_code = $is_participant ? $_GET['join_code'] : null;

// If participant, add them to the session
if ($is_participant) {
    addParticipant($session_code, $_SESSION['id'], $conn);
}

// Function to get current quiz session
function getCurrentQuizSession($quiz_id, $conn) {
    $host_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT code FROM quiz_sessions WHERE quiz_id = ? AND host_id = ? AND status = 'waiting' ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("ii", $quiz_id, $host_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['code'];
    }
    return generateAndStoreQuizCode($quiz_id, $conn);
}

// Function to generate and store a unique 6-digit quiz code
function generateAndStoreQuizCode($quiz_id, $conn) {
    $code = mt_rand(100000, 999999);
    $host_id = $_SESSION['id'];
    
    $attempts = 0;
    while ($attempts < 10) {
        $stmt = $conn->prepare("SELECT id FROM quiz_sessions WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO quiz_sessions (quiz_id, host_id, code, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $quiz_id, $host_id, $code);
            
            if ($stmt->execute()) {
                return $code;
            }
            break;
        }
        
        $code = mt_rand(100000, 999999);
        $attempts++;
    }
    
    return false;
}

// Function to get quiz title by ID
function getQuizTitle($quiz_id, $conn) {
    $stmt = $conn->prepare("SELECT title FROM quizzes WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['title'];
    }
    return 'Untitled Quiz';
}

// Function to add a participant to a quiz session
function addParticipant($session_code, $user_id, $conn) {
    // First get the session ID
    $stmt = $conn->prepare("SELECT id FROM quiz_sessions WHERE code = ?");
    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $session = $result->fetch_assoc();
    $session_id = $session['id'];
    
    // Check if user is already a participant
    $stmt = $conn->prepare("SELECT id FROM quiz_participants WHERE session_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $session_id, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        return true;
    }
    
    // Add participant
    $stmt = $conn->prepare("INSERT INTO quiz_participants (session_id, user_id, joined_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $session_id, $user_id);
    
    return $stmt->execute();
}

// Function to get active participants for a quiz session
function getParticipants($session_code, $conn) {
    $stmt = $conn->prepare("
        SELECT u.id, u.username, u.email, u.profile_pic 
        FROM quiz_participants p
        JOIN users u ON p.user_id = u.id
        JOIN quiz_sessions s ON p.session_id = s.id
        WHERE s.code = ? AND s.status != 'completed'
    ");
    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $participants = [];
    while ($row = $result->fetch_assoc()) {
        $participants[] = $row;
    }
    return $participants;
}

// Function to start a quiz session with timer
function startQuizSession($session_code, $duration_minutes, $conn) {
    $end_time = date('Y-m-d H:i:s', strtotime("+$duration_minutes minutes"));
    
    $stmt = $conn->prepare("UPDATE quiz_sessions 
                           SET status = 'active', 
                               started_at = NOW(), 
                               end_at = ?,
                               timer_duration = ?
                           WHERE code = ?");
    $stmt->bind_param("sis", $duration_minutes, $end_time, $session_code);
    
    return $stmt->execute();
}

// Function to check if quiz has started
function hasQuizStarted($session_code, $conn) {
    $stmt = $conn->prepare("SELECT status FROM quiz_sessions WHERE code = ?");
    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['status'] === 'active';
    }
    return false;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'get_participants':
                if (isset($_POST['session_code'])) {
                    $participants = getParticipants($_POST['session_code'], $conn);
                    echo json_encode(['status' => 'success', 'participants' => $participants]);
                }
                break;
                
            case 'start_quiz':
                if (isset($_POST['session_code']) && isset($_POST['duration'])) {
                    $success = startQuizSession($_POST['session_code'], $_POST['duration'], $conn);
                    echo json_encode(['status' => $success ? 'success' : 'error']);
                }
                break;
                
            case 'check_quiz_status':
                if (isset($_POST['session_code'])) {
                    $hasStarted = hasQuizStarted($_POST['session_code'], $conn);
                    echo json_encode(['status' => 'success', 'started' => $hasStarted]);
                }
                break;
                
            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
        exit;
    }
}

// For participants, check if quiz has started and redirect if needed
if ($is_participant && hasQuizStarted($session_code, $conn)) {
    header("Location: mainQuiz.php?session_code=$session_code");
    exit();
}
?>