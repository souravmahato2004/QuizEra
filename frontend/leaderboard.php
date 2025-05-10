<?php 
require_once '../backend/db.php';
require_once '../backend/leaderboardBackend.php';

if(!session_status() === PHP_SESSION_ACTIVE){
    session_start();
}

// Check if session ID is provided
$session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$user_id = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;

// Get leaderboard data
$leaderboardData = getLeaderboardData($conn, $session_id, $quiz_id, $user_id);

// Check if user is host
$is_host = false;
if ($session_id && $user_id) {
    $stmt = $conn->prepare("SELECT host_id FROM quiz_sessions WHERE id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $session = $stmt->get_result()->fetch_assoc();
    $is_host = ($session && $session['host_id'] == $user_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Leaderboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .host-only {
            display: none;
        }
        .host-view .host-only {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php
    if(!session_status() === PHP_SESSION_ACTIVE){
        session_start();
    }
    require_once '../backend/db.php';
    
    // Check if session ID is provided
    $session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
    $quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $user_id = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
    
    // Check if user is host
    $is_host = false;
    if ($session_id && $user_id) {
        $stmt = $conn->prepare("SELECT host_id FROM quiz_sessions WHERE id = ?");
        $stmt->bind_param("s",$session_id);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        $is_host = ($session && $session['host_id'] == $user_id);
    }
    ?>
    
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <button id="backButton" class="flex items-center text-purple-600 hover:text-purple-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Back to Home</span>
            </button>
            <h1 class="text-xl font-bold text-gray-800">Quiz Results</h1>
            <?php if ($is_host): ?>
            <div id="hostControls">
                <button id="toggleHostView" class="text-purple-600 hover:text-purple-800">
                    <i class="fas fa-user-shield mr-1"></i> Host View
                </button>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Side - User Results -->
            <div class="user-results-section lg:w-1/3 bg-white rounded-xl shadow-md p-6"></div>
            
            <!-- Right Side - Leaderboard -->
            <div class="leaderboard-section lg:w-2/3 bg-white rounded-xl shadow-md p-6"></div>
        </div>
    </main>

    <!-- Participant Details Modal -->
    <div id="participantModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold" id="modalParticipantName">Participant Details</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="modalContent"></div>
            </div>
        </div>
    </div>
    
    <script>
        // Pass PHP data to JavaScript
        const sessionId = <?= $session_id ?>;
        const quizId = <?= $quiz_id ?>;
        const userId = <?= $user_id ?>;
        const isHost = <?= $is_host ? 'true' : 'false' ?>;
    </script>
    <script src="../assets/js/leaderboard.js"></script>
</body>
</html>