<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../backend/db.php';
// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to login page if not logged in
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        // For AJAX requests, return error
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
        exit;
    } else {
        header("Location: login.php");
        exit;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'get_participants':
            getParticipants();
            break;
        case 'start_quiz':
            startQuiz();
            break;
        case 'pause_quiz':
            pauseQuiz();
            break;
        case 'end_quiz':
            endQuiz();
            break;
        case 'check_quiz_status':
            checkQuizStatus();
            break;
        default:
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
    exit;
}

// Main code for the page
$is_participant = false;
$title = '';
$session_code = '';

// Check if accessing as host or participant
if (isset($_GET['id'])) {
    // Host is accessing the page with quiz ID
    $quizId = $_GET['id'];
    $title = getQuizTitle($quizId, $conn);
    $session_code = getCurrentQuizSession($quizId, $conn);
} elseif (isset($_GET['session_code'])) {
    // Participant is accessing with session code
    $is_participant = true;
    $session_code = $_GET['session_code'];
    $title = getQuizTitleByCode($session_code, $conn);
    
    // Add participant to session if not already added
    addParticipantToSession($session_code, $conn);
}

// Function to get quiz title by ID
function getQuizTitle($quizId, $conn) {
    try {
        $stmt = $conn->prepare("SELECT title FROM quizzes WHERE quiz_id = ?");
        $stmt->bind_param('i', $quizId);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['title'] : 'Unknown Quiz';
    } catch (Exception $e) {
        return 'Quiz';
    }
}

// Function to get quiz title by session code
function getQuizTitleByCode($code, $conn) {
    try {
        $stmt = $conn->prepare("
            SELECT q.title 
            FROM quizzes q
            JOIN quiz_sessions qs ON q.quiz_id = qs.quiz_id
            WHERE qs.code = ? AND qs.status = 'active'
        ");
        $stmt->bind_param('s', $code); // Changed to 's' for string type
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['title'] : 'Unknown Quiz';
    } catch (Exception $e) {
        return 'Quiz';
    }
}

// Function to get or create current quiz session code
function getCurrentQuizSession($quizId, $conn) {
    try {
        // Check if there's an active session
        $stmt = $conn->prepare("
            SELECT code 
            FROM quiz_sessions 
            WHERE quiz_id = ? AND status = 'active'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->bind_param('i', $quizId);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            return $result['code'];
        }

        $sessionCode = generateSessionCode();
        $userId = $_SESSION['id'];

        // Delete only previous sessions with status 'completed'
        $stmt = $conn->prepare("
            DELETE FROM quiz_sessions 
            WHERE quiz_id = ? AND status = 'completed'
        ");
        $stmt->bind_param('i', $quizId);
        $stmt->execute();

        // Create a new active session
        $stmt = $conn->prepare("
            INSERT INTO quiz_sessions (quiz_id, code, host_id, status, created_at)
            VALUES (?, ?, ?, 'active', NOW())
        ");
        $stmt->bind_param('isi', $quizId, $sessionCode, $userId);
        $stmt->execute();

        return $sessionCode;

    } catch (Exception $e) {
        return 'ERROR';
    }
}


// Function to generate a random 6-character session code
function generateSessionCode() {
    $characters = '0123456789';
    $code = '';
    
    for ($i = 0; $i < 6; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $code;
}

// Function to add participant to session
function addParticipantToSession($code, $conn) {
    try {
        // Get session ID
        $stmt = $conn->prepare("SELECT id FROM quiz_sessions WHERE code = ? AND status = 'active'");
        $stmt->bind_param('s', $code); // Changed to 's' for string
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        if (!$result) {
            return false;
        }
        
        $sessionId = $result['id'];
        $userId = $_SESSION['id'];
        
        // Check if participant is already in session
        $stmt = $conn->prepare("
            SELECT id FROM quiz_participants 
            WHERE session_id = ? AND user_id = ?
        ");
        $stmt->bind_param('ii', $sessionId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return true; // Already added
        }
        
        // Add participant to session
        $stmt = $conn->prepare("
            INSERT INTO quiz_participants (session_id, user_id, joined_at, score)
            VALUES (?, ?, NOW(), 0)
        ");
        $stmt->bind_param('ii', $sessionId, $userId);
        $stmt->execute();
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Function to get participants for a quiz session
function getParticipants() {
    global $conn;
    
    // Get session code from POST request
    if (!isset($_POST['session_code'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Session code required']);
        return;
    }
    
    $sessionCode = $_POST['session_code'];
    
    try {
        // Get session ID
        $stmt = $conn->prepare("SELECT id FROM quiz_sessions WHERE code = ? AND status IN ('active', 'started')");
        $stmt->bind_param('s', $sessionCode);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        if (!$result) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid session code or session not active', 'debug' => ['code' => $sessionCode]]);
            return;
        }
        
        $sessionId = $result['id'];
        
        // Get participants
        $stmt = $conn->prepare("
            SELECT u.user_id, u.username, u.user_pic, qp.joined_at
            FROM quiz_participants qp
            JOIN user_info u ON qp.user_id = u.user_id
            WHERE qp.session_id = ?
            ORDER BY qp.joined_at
        ");
        $stmt->bind_param('i', $sessionId);
        $stmt->execute();
        
        // Debug - log the SQL query
        $debug_info = [
            'session_id' => $sessionId,
            'participants_count' => 0
        ];
        
        $result = $stmt->get_result();
        $participants = [];
        
        while ($row = $result->fetch_assoc()) {
            $participants[] = $row;
        }
        
        $debug_info['participants_count'] = count($participants);
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'participants' => $participants,
            'debug' => $debug_info
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// Function to start a quiz
function startQuiz() {
    global $conn;

    if (!isset($_POST['session_code']) || !isset($_POST['duration'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        return;
    }
    
    $sessionCode = intval($_POST['session_code']);
    $duration = intval($_POST['duration']);
    
    try {
        // Update session status to 'started'
        $stmt = $conn->prepare("
            UPDATE quiz_sessions
            SET status = 'started', timer_duration = ?, started_at = NOW()
            WHERE code = ? AND status = 'active'
        ");
        $stmt->bind_param('ii', $duration, $sessionCode);
        $stmt->execute();
        
        $affectedRows = $stmt->affected_rows;
        
        if ($affectedRows > 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Function to pause a quiz
function pauseQuiz() {
    global $conn;
    
    if (!isset($_POST['session_code'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Session code required']);
        return;
    }
    
    $sessionCode = $_POST['session_code'];
    
    try {
        // Update session status to 'paused'
        $stmt = $conn->prepare("
            UPDATE quiz_sessions
            SET status = 'paused', paused_at = NOW()
            WHERE code = ? AND status = 'started'
        ");
        $stmt->bind_param('s', $sessionCode);
        $stmt->execute();
        
        $affectedRows = $stmt->affected_rows;
        
        if ($affectedRows > 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Quiz not in progress']);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Function to end a quiz
function endQuiz() {
    global $conn;
    
    if (!isset($_POST['session_code'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Session code required']);
        return;
    }
    
    $sessionCode = $_POST['session_code'];
    
    try {
        // Update session status to 'completed'
        $stmt = $conn->prepare("
            UPDATE quiz_sessions
            SET status = 'completed', end_at = NOW()
            WHERE code = ? AND (status = 'started' OR status = 'paused' OR status = 'active')
        ");
        $stmt->bind_param('s', $sessionCode);
        $stmt->execute();
        
        $affectedRows = $stmt->affected_rows;
        
        if ($affectedRows > 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Function to check quiz status (for participants)
function checkQuizStatus() {
    global $conn;
    
    if (!isset($_POST['session_code'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Session code required']);
        return;
    }
    
    $sessionCode = $_POST['session_code'];
    
    try {
        // Get session status
        $stmt = $conn->prepare("SELECT status FROM quiz_sessions WHERE code = ?");
        $stmt->bind_param('s', $sessionCode);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        if (!$result) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
            return;
        }
        
        $quizStarted = ($result['status'] === 'started');
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'started' => $quizStarted,
            'quiz_status' => $result['status']
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>