<?php
session_start();
include '../backend/db.php';

// Get quiz title from GET parameter or session
$title = isset($_GET['id']) ? getQuizTitle($_GET['id'], $conn) : 'Untitled Quiz';

// Function to generate and store a unique 6-digit quiz code
function generateAndStoreQuizCode($quiz_id, $conn) {
    $code = mt_rand(100000, 999999);
    $host_id = $_SESSION['id'];
    
    // Keep generating until we get a unique code
    $attempts = 0;
    while ($attempts < 10) {
        $stmt = $conn->prepare("SELECT id FROM quiz_sessions WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            // Code is unique, insert it
            $stmt = $conn->prepare("INSERT INTO quiz_sessions (quiz_id, host_id, code, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $quiz_id, $host_id, $code);
            
            if ($stmt->execute()) {
                return $code;
            }
            break;
        }
        
        // Code exists, generate a new one
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

// Function to start a quiz session with timer
function startQuizSessionWithTimer($session_code, $duration_minutes, $conn) {
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

// Function to add a participant to a quiz session
function addParticipant($session_code, $user_id, $conn) {
    // First get the session ID
    $stmt = $conn->prepare("SELECT id FROM quiz_sessions WHERE code = ?");
    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false; // Session doesn't exist
    }
    
    $session = $result->fetch_assoc();
    $session_id = $session['id'];
    
    // Check if user is already a participant
    $stmt = $conn->prepare("SELECT id FROM quiz_participants WHERE session_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $session_id, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        return true; // Already a participant
    }
    
    // Add participant
    $stmt = $conn->prepare("INSERT INTO quiz_participants (session_id, user_id, joined_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $session_id, $user_id);
    
    return $stmt->execute();
}

// Function to end a quiz session
function endQuizSession($session_code, $conn) {
    // Update session status
    $stmt = $conn->prepare("UPDATE quiz_sessions SET status = 'completed', end_at = NOW() WHERE code = ?");
    $stmt->bind_param("s", $session_code);
    
    return $stmt->execute();
}

// Function to get active participants for a quiz session
function getParticipants($session_code, $conn) {
    $stmt = $conn->prepare("
        SELECT u.user_id, u.username, u.user_email, u.user_pic
        FROM quiz_participants p
        JOIN user_info u ON p.user_id = u.user_id
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

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_session':
                if (isset($_POST['quiz_id'])) {
                    $code = generateAndStoreQuizCode($_POST['quiz_id'], $conn);
                    if ($code) {
                        echo json_encode(['status' => 'success', 'code' => $code]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to create session']);
                    }
                }
                break;
                
            case 'get_participants':
                if (isset($_POST['session_code'])) {
                    $participants = getParticipants($_POST['session_code'], $conn);
                    echo json_encode(['status' => 'success', 'participants' => $participants]);
                }
                break;
                
            case 'start_quiz':
                if (isset($_POST['session_code']) && isset($_POST['duration'])) {
                    $success = startQuizSessionWithTimer($_POST['session_code'], $_POST['duration'], $conn);
                    echo json_encode(['status' => $success ? 'success' : 'error']);
                }
                break;
                
            case 'end_quiz':
                if (isset($_POST['session_code'])) {
                    $success = endQuizSession($_POST['session_code'], $conn);
                    echo json_encode(['status' => $success ? 'success' : 'error']);
                }
                break;
                
            case 'join_quiz':
                if (isset($_POST['session_code']) && isset($_SESSION['user_id'])) {
                    $success = addParticipant($_POST['session_code'], $_SESSION['user_id'], $conn);
                    echo json_encode(['status' => $success ? 'success' : 'error']);
                }
                break;
                
            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
        exit;
    }
}

// For initial page load
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
?>