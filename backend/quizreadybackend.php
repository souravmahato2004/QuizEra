<?php
session_start();

include '../backend/db.php';

// Get quiz title from GET parameter or session
$title = isset($_GET['quiz_id']) ? getQuizTitle($_GET['quiz_id'], $conn) : 'Untitled Quiz';

// Function to generate a unique 6-digit quiz code
function generateQuizCode() {
    $code = mt_rand(100000, 999999);
    
    // In a real application, you would check if this code exists in the database
    // and generate a new one if it does
    return $code;
}

// Function to get quiz title by ID
function getQuizTitle($quiz_id, $conn) {
    $stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['title'];
    }
    return 'Untitled Quiz';
}

// Function to create a new quiz session for hosting
function createQuizSession($quiz_id, $conn) {
    $code = generateQuizCode();
    $host_id = $_SESSION['user_id']; // Assuming user is logged in
    
    $stmt = $conn->prepare("INSERT INTO quiz_sessions (quiz_id, host_id, code, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $quiz_id, $host_id, $code);
    
    if ($stmt->execute()) {
        return $code;
    }
    return false;
}

// Function to get active participants for a quiz session
function getParticipants($session_code, $conn) {
    $stmt = $conn->prepare("
        SELECT u.id, u.username, u.email 
        FROM participants p
        JOIN users u ON p.user_id = u.id
        WHERE p.session_code = ?
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

// Function to save quiz settings
function saveQuizSettings($quiz_id, $settings, $conn) {
    $settings_json = json_encode($settings);
    
    $stmt = $conn->prepare("UPDATE quizzes SET settings = ? WHERE id = ?");
    $stmt->bind_param("si", $settings_json, $quiz_id);
    
    return $stmt->execute();
}

// Function to start a quiz session
function startQuizSession($session_code, $conn) {
    $stmt = $conn->prepare("UPDATE quiz_sessions SET status = 'active', started_at = NOW() WHERE code = ?");
    $stmt->bind_param("s", $session_code);
    
    return $stmt->execute();
}

// Handle form submissions for the hosting page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_session':
                if (isset($_POST['quiz_id'])) {
                    $code = createQuizSession($_POST['quiz_id'], $conn);
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
                
            case 'save_settings':
                if (isset($_POST['quiz_id']) && isset($_POST['settings'])) {
                    $success = saveQuizSettings($_POST['quiz_id'], $_POST['settings'], $conn);
                    echo json_encode(['status' => $success ? 'success' : 'error']);
                }
                break;
                
            case 'start_quiz':
                if (isset($_POST['session_code'])) {
                    $success = startQuizSession($_POST['session_code'], $conn);
                    echo json_encode(['status' => $success ? 'success' : 'error']);
                }
                break;
        }
    }
    exit;
}

// Close connection (not always needed as PHP will close it automatically)
// $conn->close();
?>