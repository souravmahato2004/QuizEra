<?php
header("Content-Type: application/json");
require_once '../backend/db.php';

session_start();

// Generate random session code
function generateSessionCode() {
    return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);
}

// GET: Fetch quiz data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if (!isset($_GET['session_code'])) {
            throw new Exception("Session code required");
        }
        
        $sessionCode = $_GET['session_code'];
        
        // Get session details
        $stmt = executeQuery($conn, 
            "SELECT s.id, s.quiz_id, q.title, q.description 
             FROM quiz_sessions s
             JOIN quizzes q ON s.quiz_id = q.quiz_id
             WHERE s.code = ? AND s.is_active = 1", 
            [$sessionCode]);
        
        $session = $stmt->get_result()->fetch_assoc();
        if (!$session) {
            throw new Exception("Session not found or inactive");
        }
        
        // Get questions
        $stmt = executeQuery($conn,
            "SELECT * FROM quiz_slides 
             WHERE quiz_id = ? 
             ORDER BY position",
            [$session['quiz_id']]);
        
        $questions = [];
        $slides = $stmt->get_result();
        
        while ($slide = $slides->fetch_assoc()) {
            $question = [
                'slide_id' => $slide['slide_id'],
                'text' => $slide['question'],
                'type' => $slide['question_type'],
                'image' => $slide['image_url'],
                'options' => [],
                'correct_answer' => null
            ];
            
            // Get options for multiple choice
            if ($slide['question_type'] === 'multiple') {
                $stmt = executeQuery($conn,
                    "SELECT * FROM slide_options 
                     WHERE slide_id = ? 
                     ORDER BY position",
                    [$slide['slide_id']]);
                
                $options = $stmt->get_result();
                while ($option = $options->fetch_assoc()) {
                    $question['options'][] = [
                        'text' => $option['option_text'],
                        'is_correct' => (bool)$option['is_correct']
                    ];
                    
                    if ($option['is_correct']) {
                        $question['correct_answer'] = count($question['options']) - 1;
                    }
                }
            } 
            // Get answer for text questions
            else {
                $stmt = executeQuery($conn,
                    "SELECT correct_answer FROM slide_answers 
                     WHERE slide_id = ?",
                    [$slide['slide_id']]);
                
                $answer = $stmt->get_result()->fetch_assoc();
                $question['correct_answer'] = $answer ? $answer['correct_answer'] : null;
            }
            
            $questions[] = $question;
        }
        
        // Get participants
        $stmt = executeQuery($conn,
            "SELECT u.user_id, u.username, u.name 
             FROM quiz_participants p
             JOIN user_info u ON p.user_id = u.user_id
             WHERE p.session_id = ?",
            [$session['id']]);
        
        $participants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Format response
        echo json_encode([
            'success' => true,
            'quiz' => [
                'title' => $session['title'],
                'description' => $session['description'],
                'duration' => 900 // 15 minutes default
            ],
            'questions' => $questions,
            'participants' => array_map(function($p) {
                return [
                    'id' => $p['user_id'],
                    'name' => $p['name'] ?: $p['username'],
                    'avatar' => strtoupper(substr($p['name'] ?: $p['username'], 0, 1)),
                    'hasAnswered' => false
                ];
            }, $participants)
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

// POST: Create session or submit responses
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_SESSION['id'])) {
            throw new Exception("Authentication required");
        }
        
        $userId = $_SESSION['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Create new session
        if (isset($data['quiz_id'])) {
            $quizId = (int)$data['quiz_id'];
            
            // Verify quiz ownership
            $stmt = executeQuery($conn,
                "SELECT quiz_id FROM quizzes 
                 WHERE quiz_id = ? AND owner_id = ?",
                [$quizId, $userId], "ii");
            
            if ($stmt->get_result()->num_rows === 0) {
                throw new Exception("Quiz not found or access denied");
            }
            
            // Generate unique session code
            $code = generateSessionCode();
            $attempts = 0;
            
            // Ensure code is unique
            while ($attempts < 5) {
                $stmt = executeQuery($conn,
                    "SELECT id FROM quiz_sessions 
                     WHERE code = ?",
                    [$code]);
                
                if ($stmt->get_result()->num_rows === 0) {
                    break;
                }
                
                $code = generateSessionCode();
                $attempts++;
            }
            
            if ($attempts >= 5) {
                throw new Exception("Failed to generate unique session code");
            }
            
            // Create session
            $stmt = executeQuery($conn,
                "INSERT INTO quiz_sessions 
                 (code, quiz_id, created_by) 
                 VALUES (?, ?, ?)",
                [$code, $quizId, $userId], "sii");
            
            $sessionId = $stmt->insert_id;
            
            // Add creator as participant
            executeQuery($conn,
                "INSERT INTO quiz_participants 
                 (session_id, user_id) 
                 VALUES (?, ?)",
                [$sessionId, $userId], "ii");
            
            echo json_encode([
                'success' => true,
                'session_code' => $code
            ]);
        }
        
        // Submit responses
        elseif (isset($_GET['session_code']) && isset($data['responses'])) {
            $sessionCode = $_GET['session_code'];
            $responses = $data['responses'];
            
            // Verify session exists
            $stmt = executeQuery($conn,
                "SELECT id, quiz_id FROM quiz_sessions 
                 WHERE code = ?",
                [$sessionCode]);
            
            $session = $stmt->get_result()->fetch_assoc();
            if (!$session) {
                throw new Exception("Session not found");
            }
            
            // Verify participant
            $stmt = executeQuery($conn,
                "SELECT id FROM quiz_participants 
                 WHERE session_id = ? AND user_id = ?",
                [$session['id'], $userId], "ii");
            
            if ($stmt->get_result()->num_rows === 0) {
                throw new Exception("Not a participant in this session");
            }
            
            $score = 0;
            $quizId = $session['quiz_id'];
            
            // Process each response
            foreach ($responses as $r) {
                if (!isset($r['slide_id']) || !isset($r['answer'])) {
                    continue;
                }
                
                $slideId = (int)$r['slide_id'];
                $answer = $r['answer'];
                $isCorrect = false;
                
                // Verify slide belongs to quiz
                $stmt = executeQuery($conn,
                    "SELECT question_type FROM quiz_slides 
                     WHERE slide_id = ? AND quiz_id = ?",
                    [$slideId, $quizId], "ii");
                
                $slide = $stmt->get_result()->fetch_assoc();
                if (!$slide) {
                    continue;
                }
                
                // Check correctness based on question type
                if ($slide['question_type'] === 'multiple') {
                    $position = is_numeric($answer) ? (int)$answer : null;
                    
                    $stmt = executeQuery($conn,
                        "SELECT is_correct FROM slide_options 
                         WHERE slide_id = ? AND position = ?",
                        [$slideId, $position], "ii");
                    
                    $option = $stmt->get_result()->fetch_assoc();
                    $isCorrect = $option ? (bool)$option['is_correct'] : false;
                } 
                else {
                    $stmt = executeQuery($conn,
                        "SELECT correct_answer FROM slide_answers 
                         WHERE slide_id = ?",
                        [$slideId], "i");
                    
                    $correctAnswer = $stmt->get_result()->fetch_assoc()['correct_answer'];
                    $isCorrect = strtolower(trim($answer)) === strtolower(trim($correctAnswer));
                }
                
                if ($isCorrect) {
                    $score++;
                }
                
                // Save response
                executeQuery($conn,
                    "INSERT INTO quiz_responses 
                     (quiz_id, slide_id, user_id, answer, is_correct) 
                     VALUES (?, ?, ?, ?, ?)",
                    [$quizId, $slideId, $userId, $answer, $isCorrect], "iiisi");
            }
            
            echo json_encode([
                'success' => true,
                'score' => $score,
                'total_questions' => count($responses)
            ]);
        }
        
        else {
            throw new Exception("Invalid request");
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

// Invalid method
else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
?>