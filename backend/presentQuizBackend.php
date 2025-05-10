<?php
// api.php
header("Content-Type: application/json");
require_once 'db.php'; // Your database connection file

session_start();

// Get quiz data by session code
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['session_code'])) {
    $sessionCode = $_GET['session_code'];
    
    try {
        // Get quiz ID from session code (you'll need a sessions table)
        $stmt = $conn->prepare("SELECT quiz_id FROM quiz_sessions WHERE code = ?");
        $stmt->bind_param('s',$sessionCode);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        
        if (!$session) {
            http_response_code(404);
            echo json_encode(['error' => 'Session not found']);
            exit;
        }
        
        $quizId = $session['quiz_id'];
        
        // Get quiz details
        $stmt = $conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
        $stmt->bind_param('i',$quizId);
        $stmt->execute();
        $quiz = $stmt->get_result()->fetch_assoc();
        
        // Get quiz slides (questions)
        $stmt = $conn->prepare("SELECT * FROM quiz_slides WHERE quiz_id = ? ORDER BY position");
        $stmt->bind_param('i',$quizId);
        $stmt->execute();
        $slides = $stmt->get_result();
        
        // Get options for each question
        $questions = [];
        while($slide=$slides->fetch_assoc()) {
            $question = [
                'slide_id' => $slide['slide_id'],
                'text' => $slide['question'],
                'type' => $slide['question_type'],
                'image' => $slide['image_url'],
                'options' => [],
                'correct_answer' => null
            ];
            
            // For multiple choice questions, get options
            if ($slide['question_type'] === 'multiple') {
                $stmt = $conn->prepare("SELECT * FROM slide_options WHERE slide_id = ? ORDER BY position");
                $stmt->bind_param('i',$slide['slide_id']);
                $stmt->execute();
                $options = $stmt->get_result();
                
                while ($option =$options->fetch_assoc()) {
                    $question['options'][] = [
                        'text' => $option['option_text'],
                        'is_correct' => (bool)$option['is_correct']
                    ];
                    
                    if ($option['is_correct']) {
                        $question['correct_answer'] = count($question['options']) - 1;
                    }
                }
            }
            // For fill-in-the-blank questions, get correct answer
            elseif (in_array($slide['question_type'], ['fillblank', 'shortanswer'])) {
                $stmt = $conn->prepare("SELECT correct_answer FROM slide_answers WHERE slide_id = ?");
                $stmt->bind_param('i',$slide['slide_id']);
                $stmt->execute();
                $answer = $stmt->get_result()->fetch_assoc();
                
                if ($answer) {
                    $question['correct_answer'] = $answer['correct_answer'];
                }
            }
            
            $questions[] = $question;
        }
        // get session id
        $stmt = $conn->prepare("SELECT * FROM quiz_sessions WHERE code = ?");
        $stmt->bind_param('s',$sessionCode);
        $stmt->execute();
        $id = $stmt->get_result()->fetch_assoc();
        $sessionId=$id['id'];

        // Get participants in this session
        $stmt = $conn->prepare("
            SELECT u.user_id, u.username, u.name 
            FROM quiz_participants p
            JOIN user_info u ON p.user_id = u.user_id
            WHERE p.session_id = ?
        ");
        $stmt->bind_param('i',$sessionId);
        $stmt->execute();
        $participants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Prepare response
        $response = [
            'quiz' => [
                'title' => $quiz['title'],
                'description' => $quiz['description'],
                'duration' => 15 * 60 // Default duration, can be from DB
            ],
            'questions' => $questions,
            'participants' => array_map(function($p) {
                return [
                    'id' => $p['user_id'],
                    'name' => $p['name'] ?: $p['username'],
                    'avatar' => strtoupper(substr($p['name'] ?: $p['username'], 0, 1)),
                    'hasAnswered' => false // You'd track this in real app
                ];
            }, $participants)
        ];
        
        echo json_encode($response);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Submit quiz responses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['session_code'])) {
    $sessionCode = $_GET['session_code'];
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_SESSION['id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        $userId = $_SESSION['id'];
        $responses = $data['responses'];
        $score = 0;
        
        foreach ($responses as $response) {
            $slideId = $response['slide_id'];
            $answer = $response['answer'];
            $isCorrect = false;
            
            // Check if answer is correct
            $stmt = $pdo->prepare("
                SELECT 
                    CASE 
                        WHEN s.question_type = 'multiple' THEN o.is_correct
                        WHEN s.question_type IN ('fillblank', 'shortanswer') THEN LOWER(a.correct_answer) = LOWER(?)
                        ELSE NULL
                    END AS is_correct
                FROM quiz_slides s
                LEFT JOIN slide_options o ON s.slide_id = o.slide_id AND o.position = ?
                LEFT JOIN slide_answers a ON s.slide_id = a.slide_id
                WHERE s.slide_id = ?
            ");
            
            $position = is_numeric($answer) ? (int)$answer : null;
            $stmt->execute([$answer, $position, $slideId]);
            $result = $stmt->fetch();
            
            if ($result && $result['is_correct']) {
                $isCorrect = true;
                $score++;
            }
            
            // Save response
            $stmt = $pdo->prepare("
                INSERT INTO quiz_responses 
                (quiz_id, slide_id, user_id, answer, is_correct, submitted_at)
                VALUES (
                    (SELECT quiz_id FROM quiz_sessions WHERE session_code = ?),
                    ?, ?, ?, ?, NOW()
                )
            ");
            $stmt->execute([$sessionCode, $slideId, $userId, $answer, $isCorrect]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'score' => $score,
            'total_questions' => count($responses)
        ]);
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>