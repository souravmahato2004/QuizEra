<?php

require_once '../backend/db.php';
session_start();

function getLeaderboardData($conn, $session_id, $quiz_id, $user_id) {
    // Get ALL participants for this session
    $stmt = $conn->prepare("
        SELECT qp.user_id, u.name, qp.score, qp.joined_at,
               RANK() OVER (ORDER BY qp.score DESC) as rank
        FROM quiz_participants qp
        JOIN user_info u ON qp.user_id = u.user_id
        WHERE qp.session_id = ?
        ORDER BY qp.score DESC
    ");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $allParticipants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get total questions count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM quiz_slides qs
        JOIN quiz_sessions qses ON qs.quiz_id = qses.quiz_id
        WHERE qses.id = ?
    ");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $totalQuestions = $stmt->get_result()->fetch_assoc()['count'];

    // Find current user
    $currentUser = null;
    foreach ($allParticipants as $participant) {
        if ($participant['user_id'] == $user_id) {
            $currentUser = $participant;
            break;
        }
    }

    if (!$currentUser) {
        return ['error' => 'Participant not found'];
    }

    // Get question results for current user
    $stmt = $conn->prepare("
        SELECT qs.question, qr.is_correct, qr.submitted_at
        FROM quiz_responses qr
        JOIN quiz_slides qs ON qr.slide_id = qs.slide_id
        JOIN quiz_sessions qses ON qr.quiz_id = qses.quiz_id
        WHERE qr.user_id = ? AND qses.id = ?
        ORDER BY qs.position
    ");
    $stmt->bind_param("ii", $user_id, $session_id);
    $stmt->execute();
    $questionResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Calculate time taken (simplified)
    $timeTaken = 'N/A';
    if ($currentUser['joined_at']) {
        $joined = new DateTime($currentUser['joined_at']);
        $lastResponse = $questionResults ? end($questionResults)['submitted_at'] : null;
        if ($lastResponse) {
            $last = new DateTime($lastResponse);
            $interval = $joined->diff($last);
            $timeTaken = $interval->format('%i:%s');
        }
    }

    // Prepare base data structure
    $data = [
        'quizTitle' => 'Quiz Results',
        'user' => [
            'id' => $currentUser['user_id'],
            'name' => $currentUser['name'],
            'score' => $currentUser['score'],
            'totalQuestions' => $totalQuestions,
            'timeTaken' => $timeTaken,
            'rank' => $currentUser['rank'],
            'questionResults' => array_map(function($q) {
                return [
                    'question' => $q['question'],
                    'correct' => (bool)$q['is_correct'],
                    'timeSpent' => 'N/A'
                ];
            }, $questionResults)
        ],
        'participants' => []
    ];

    // Prepare participant data
    foreach ($allParticipants as $participant) {
        $participantData = [
            'id' => $participant['user_id'],
            'name' => $participant['name'],
            'score' => $participant['score'],
            'totalQuestions' => $totalQuestions,
            'timeTaken' => 'N/A',
            'rank' => $participant['rank']
        ];

        // If host is viewing, include question results for all participants
        $is_host = false;
        if ($session_id && $user_id) {
            $stmt = $conn->prepare("SELECT host_id FROM quiz_sessions WHERE id = ?");
            $stmt->bind_param("i", $session_id);
            $stmt->execute();
            $session = $stmt->get_result()->fetch_assoc();
            $is_host = ($session && $session['host_id'] == $user_id);
        }

        if ($is_host) {
            $stmt = $conn->prepare("
                SELECT qs.question, qr.is_correct, qr.submitted_at
                FROM quiz_responses qr
                JOIN quiz_slides qs ON qr.slide_id = qs.slide_id
                JOIN quiz_sessions qses ON qr.quiz_id = qses.quiz_id
                WHERE qr.user_id = ? AND qses.id = ?
                ORDER BY qs.position
            ");
            $stmt->bind_param("ii", $participant['user_id'], $session_id);
            $stmt->execute();
            $participantQuestions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $participantData['questionResults'] = array_map(function($q) {
                return [
                    'question' => $q['question'],
                    'correct' => (bool)$q['is_correct'],
                    'timeSpent' => 'N/A'
                ];
            }, $participantQuestions);
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

    try {
        $data = getLeaderboardData($conn, $session_id, $quiz_id, $user_id);
        echo json_encode($data);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
?>