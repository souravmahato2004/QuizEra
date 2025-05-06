<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Debug information - remove in production
error_log("Session data: " . print_r($_SESSION, true));

// Check if code parameter exists
if (!isset($_POST['code'])) {
    echo json_encode([
        'valid' => false,
        'message' => 'No quiz code provided'
    ]);
    exit();
}

$code = trim($_POST['code']);
// Validate code format (6 digits)
if (!preg_match('/^\d{6}$/', $code)) {
    echo json_encode([
        'valid' => false,
        'message' => 'Invalid code format. Please enter a 6-digit number.'
    ]);
    exit();
}

// Check if user is logged in - using $_SESSION['id'] instead of user_id
if (!isset($_SESSION['id'])) {
    echo json_encode([
        'valid' => false,
        'message' => 'You must be logged in to join a quiz.'
    ]);
    exit();
}

try {
    // First, let's check what tables actually exist in the database
    $tableQuery = $conn->query("SHOW TABLES");
    $tables = [];
    while($row = $tableQuery->fetch_array()) {
        $tables[] = $row[0];
    }
    error_log("Available tables: " . implode(", ", $tables));
    
    // Begin transaction
    $conn->begin_transaction();

    // Modified query to work with your actual database structure
    // Assuming you have tables: quiz_sessions, quizzes, and some table for users
    // Let's try to detect the correct table names
    
    $userTableName = "users"; // Default, but we'll check if it exists
    
    // Check different possible user table names
    $possibleUserTables = ["users", "user", "user_accounts", "accounts"];
    foreach ($possibleUserTables as $tableName) {
        if (in_array($tableName, $tables)) {
            $userTableName = $tableName;
            break;
        }
    }
    
    // Modified query based on tables that exist
    $query = "
        SELECT 
            s.id, 
            s.quiz_id,
            s.status, 
            s.started_at,
            s.end_at,
            q.title AS quiz_title
        FROM quiz_sessions s
        JOIN quizzes q ON s.quiz_id = q.quiz_id
        WHERE s.code = ? AND s.status != 'completed'
    ";
    
    // Add host information if the user table exists
    if (in_array($userTableName, $tables)) {
        $query = "
            SELECT 
                s.id, 
                s.quiz_id,
                s.status, 
                s.started_at,
                s.end_at,
                q.title AS quiz_title,
                u.username AS host_name
            FROM quiz_sessions s
            JOIN quizzes q ON s.quiz_id = q.id
            JOIN $userTableName u ON s.host_id = u.id
            WHERE s.code = ? AND s.status != 'completed'
        ";
    }
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $conn->rollback();
        echo json_encode([
            'valid' => false,
            'message' => 'Invalid quiz code or quiz has ended. Please check the code and try again.'
        ]);
        exit();
    }

    $session = $result->fetch_assoc();

    // 2. Check if quiz has already started
    if ($session['status'] === 'active') {
        // If quiz is active, immediately redirect to quiz page
        echo json_encode([
            'valid' => true,
            'redirect' => 'quizReady.php?session_code='.$code,
            'session' => [
                'id' => $session['id'],
                'quiz_title' => $session['quiz_title'],
                'host_name' => $session['host_name'] ?? 'Unknown Host'
            ]
        ]);
        $conn->commit();
        exit();
    }

    // 3. Check if user is already a participant
    $stmt = $conn->prepare("
        SELECT id FROM quiz_participants 
        WHERE session_id = ? AND user_id = ?
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    // Use $_SESSION['id'] instead of $_SESSION['user_id']
    $stmt->bind_param("ii", $session['id'], $_SESSION['id']);
    $stmt->execute();
    
    $isParticipant = $stmt->get_result()->num_rows > 0;

    if (!$isParticipant) {
        // 4. Add user as participant if not already joined
        $stmt = $conn->prepare("
            INSERT INTO quiz_participants (session_id, user_id, joined_at) 
            VALUES (?, ?, NOW())
        ");
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        // Use $_SESSION['id'] instead of $_SESSION['user_id']
        $stmt->bind_param("ii", $session['id'], $_SESSION['id']);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to add participant: " . $stmt->error);
        }
    }

    // Commit transaction
    $conn->commit();

    // Return success response with session details
    echo json_encode([
        'valid' => true,
        'redirect' => 'quizready.php?join_code='.$code,
        'session' => [
            'id' => $session['id'],
            'quiz_id' => $session['quiz_id'],
            'quiz_title' => $session['quiz_title'],
            'host_name' => $session['host_name'] ?? 'Unknown Host',
            'is_participant' => true
        ]
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }
    error_log("Error verifying quiz code: " . $e->getMessage());
    echo json_encode([
        'valid' => false,
        'message' => 'An error occurred while verifying the quiz code: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?>