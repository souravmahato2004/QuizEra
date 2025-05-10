<?php

require_once '../backend/db.php';
session_start();

function getLeaderboardData($conn, $session_id, $quiz_id, $user_id, $host_id, $is_host) {
    // Get participants (exclude host if they're viewing)
    $excludeHost = $is_host ? "AND qp.user_id != ?" : "";
    $paramTypes = $is_host ? "ii" : "i";
    $params = $is_host ? [$session_id, $host_id] : [$session_id];

    $stmt = $conn->prepare("
        SELECT qp.user_id, u.username, qp.score, qp.joined_at,
               RANK() OVER (ORDER BY qp.score DESC) as rank
        FROM quiz_participants qp
        JOIN user_info u ON qp.user_id = u.user_id
        WHERE qp.session_id = ? $excludeHost
        ORDER BY qp.score DESC
        LIMIT 10
    ");
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $allParticipants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get total questions count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM quiz_slides WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $totalQuestions = $stmt->get_result()->fetch_assoc()['count'];

    // Get current user data (even if host)
    $currentUser = null;
    if ($is_host) {
        $stmt = $conn->prepare("SELECT username FROM user_info WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        $currentUser = [
            'user_id' => $user_id,
            'username' => $user['username'],
            'score' => 0,
            'rank' => null,
            'joined_at' => null
        ];
    } else {
        foreach ($allParticipants as $participant) {
            if ($participant['user_id'] == $user_id) {
                $currentUser = $participant;
                break;
            }
        }
        if (!$currentUser) {
            return ['error' => 'Participant not found'];
        }
    }

    // Get question results with correct answers
    $questionResults = [];
    if (!$is_host) { // Only get detailed results for actual participants
        $stmt = $conn->prepare("
            SELECT 
                qs.question,
                qr.answer,
                qr.is_correct, 
                qr.submitted_at
            FROM quiz_responses qr
            JOIN quiz_slides qs ON qr.slide_id = qs.slide_id
            WHERE qr.user_id = ? AND qr.quiz_id = ?
            ORDER BY qs.position
        ");
        $stmt->bind_param("ii", $user_id, $quiz_id);
        $stmt->execute();
        $questionResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Calculate time taken
    $timeTaken = 'N/A';
    if (!empty($currentUser['joined_at']) && !empty($questionResults)) {
        $joined = new DateTime($currentUser['joined_at']);
        $lastResponse = end($questionResults)['submitted_at'];
        $last = new DateTime($lastResponse);
        $interval = $joined->diff($last);
        $timeTaken = $interval->format('%i mins %s secs');
    }

    // Prepare base data
    $data = [
        'quizTitle' => 'Quiz Results',
        'user' => [
            'id' => $currentUser['user_id'],
            'username' => $currentUser['username'],
            'score' => $currentUser['score'],
            'totalQuestions' => $totalQuestions,
            'timeTaken' => $timeTaken,
            'rank' => $currentUser['rank'],
            'questionResults' => array_map(function($q) {
                return [
                    'question' => $q['question'],
                    'correct' => (bool)$q['is_correct'],
                    // 'user_answer' => $q['user_answer'],
                    'correct_answer' => $q['answer'],
                    'timeSpent' => 'N/A'
                ];
            }, $questionResults)
        ],
        'participants' => []
    ];

    // Prepare participant data (with question details for host view)
    foreach ($allParticipants as $participant) {
        $participantData = [
            'id' => $participant['user_id'],
            'username' => $participant['username'],
            'score' => $participant['score'],
            'totalQuestions' => $totalQuestions,
            'timeTaken' => 'N/A',
            'rank' => $participant['rank']
        ];

        if ($is_host) {
            $stmt = $conn->prepare("
                SELECT 
                    qs.question,
                    qr.answer,
                    qr.is_correct
                FROM quiz_responses qr
                JOIN quiz_slides qs ON qr.slide_id = qs.slide_id
                WHERE qr.user_id = ? AND qr.quiz_id = ?
                ORDER BY qs.position
            ");
            $stmt->bind_param("ii", $participant['user_id'], $quiz_id);
            $stmt->execute();
            $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $participantData['questionResults'] = array_map(function($q) {
                return [
                    'question' => $q['question'],
                    'correct' => (bool)$q['is_correct'],
                    'user_answer' => $q['answer'],
                    'timeSpent' => 'N/A'
                ];
            }, $questions);
        }

        $data['participants'][] = $participantData;
    }

    return $data;
}
// Prepare participant data


// Only execute if called directly for AJAX requests
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    // Get and validate parameters
    $session_id = filter_input(INPUT_GET, 'session_id', FILTER_VALIDATE_INT);
    $quiz_id = filter_input(INPUT_GET, 'quiz_id', FILTER_VALIDATE_INT);
    $user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

    if (!$session_id || !$quiz_id || !$user_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid parameters']);
        exit;
    }

    $is_host=($user_id==$hostId);

    try {
        $data = getLeaderboardData($conn, $session_id, $quiz_id, $user_id,$hostId,$is_host);
        echo json_encode($data);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
?>