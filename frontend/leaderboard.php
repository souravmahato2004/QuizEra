<?php 
require_once '../backend/db.php';
require_once '../backend/leaderboardBackend.php';

// Start session only if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Get parameters
$session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$user_id = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;

// Check host status
$is_host = false;
$host_id = null;
if ($session_id && $user_id) {
    $stmt = $conn->prepare("SELECT host_id FROM quiz_sessions WHERE id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $session = $stmt->get_result()->fetch_assoc();
    if ($session) {
        $host_id = $session['host_id'];
        $is_host = ($host_id == $user_id);
    }
}

// Get leaderboard data (exclude host from participants if they're viewing)
$leaderboardData = getLeaderboardData($conn, $session_id, $quiz_id, $user_id, $host_id, $is_host);
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
        body { font-family: 'Inter', sans-serif; }
        .host-only { display: none; }
        .host-view .host-only { display: block; }
        .podium-1st { height: 220px; background-color: #f3e8ff; }
        .podium-2nd { height: 180px; background-color: #fef9c3; }
        .podium-3rd { height: 160px; background-color: #bfdbfe; }
        .correct-answer { border-left: 4px solid #10b981; }
        .incorrect-answer { border-left: 4px solid #ef4444; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen <?= $is_host ? 'host-view' : '' ?>">
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
            <div class="user-results-section lg:w-1/3 bg-white rounded-xl shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Results</h2>
                
                <?php if (isset($leaderboardData['user'])): ?>
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-4xl mb-4">
                            <?= substr($leaderboardData['user']['username'], 0, 1) ?>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($leaderboardData['user']['username']) ?></h3>
                        <p class="text-gray-500"><?= $is_host ? 'Host' : 'Rank: '.($leaderboardData['user']['rank'] ?? 'N/A') ?></p>
                    </div>
                    <?php if(!$is_host): ?>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Score</span>
                            <span class="font-medium"><?= $leaderboardData['user']['score'] ?>/<?= $leaderboardData['user']['totalQuestions'] ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Accuracy</span>
                            <span class="font-medium">
                                <?= round(($leaderboardData['user']['score'] / $leaderboardData['user']['totalQuestions']) * 100) ?>%
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Time Taken</span>
                            <span class="font-medium"><?= $leaderboardData['user']['timeTaken'] ?? 'N/A' ?></span>
                        </div>
                    </div>
                    
                    <!-- Question Breakdown -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-700 mb-3">Question Breakdown</h4>
                        <div class="space-y-3">
                            <?php foreach ($leaderboardData['user']['questionResults'] as $i => $q): ?>
                                <div class="p-3 rounded-lg <?= $q['correct'] ? 'bg-green-50' : 'bg-red-50' ?>">
                                    <div class="font-medium mb-2">Q<?= $i+1 ?>: <?= htmlspecialchars($q['question']) ?></div>
                                    <div class="text-sm mb-1">Your answer: 
                                        <span class="<?= $q['correct'] ? 'text-green-700' : 'text-red-700' ?>">
                                            <?= htmlspecialchars($q['correct_answer'] ?? 'No answer') ?>
                                        </span>
                                    </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                <?php else: ?>
                    <p class="text-red-500">No user data available</p>
                <?php endif; ?>
            </div>
            
            <!-- Right Side - Leaderboard -->
            <div class="leaderboard-section lg:w-2/3 bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Leaderboard</h2>
                    <span class="text-gray-500">
                        <?= count($leaderboardData['participants']) ?> Participants
                    </span>
                </div>
                
                <?php if (!empty($leaderboardData['participants'])): ?>
                    <!-- Top 3 Podium -->
                    <div class="flex justify-center items-end gap-4 mb-8">
                        <?php 
                        $topThree = array_slice($leaderboardData['participants'], 0, 3);
                        $podiumOrder = isset($topThree[1]) ? [$topThree[1], $topThree[0], $topThree[2] ?? null] : $topThree;
                        
                        foreach ($podiumOrder as $index => $participant): 
                            if (!$participant) continue;
                            
                            $positionClass = ['podium-2nd', 'podium-1st', 'podium-3rd'][$index] ?? '';
                            $textColor = $positionClass === 'podium-1st' ? 'text-purple-700' : 
                                        ($positionClass === 'podium-2nd' ? 'text-yellow-700' : 'text-blue-700');
                        ?>
                            <div class="flex-1 max-w-[200px] <?= $positionClass ?> rounded-t-lg p-4 flex flex-col items-center 
                                <?= $is_host ? 'cursor-pointer hover:shadow-md' : '' ?>"
                                <?= $is_host ? 'onclick="showParticipantDetails('.$participant['id'].')"' : '' ?>>
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold mb-2 <?= $textColor ?>">
                                    <?= $participant['rank'] ?>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center font-bold mb-2 text-2xl <?= $textColor ?>">
                                    <?= substr($participant['username'], 0, 1) ?>
                                </div>
                                <h3 class="font-medium text-center"><?= htmlspecialchars($participant['username']) ?></h3>
                                <p class="text-sm text-gray-500"><?= $participant['score'] ?>/<?= $participant['totalQuestions'] ?></p>
                                <p class="text-xs text-gray-400 mt-1"><?= $participant['timeTaken'] ?? 'N/A' ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Rest of Participants -->
                    <div class="space-y-3">
                        <?php foreach (array_slice($leaderboardData['participants'], 3) as $participant): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors
                                <?= $is_host ? 'cursor-pointer' : '' ?>"
                                <?= $is_host ? 'onclick="showParticipantDetails('.$participant['id'].')"' : '' ?>>
                                <div class="flex items-center">
                                    <span class="w-8 text-center font-medium mr-4"><?= $participant['rank'] ?></span>
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-bold mr-3">
                                        <?= substr($participant['name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div><?= htmlspecialchars($participant['name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= $participant['timeTaken'] ?? 'N/A' ?></div>
                                    </div>
                                </div>
                                <span class="font-medium"><?= $participant['score'] ?>/<?= $participant['totalQuestions'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No participants data available</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Participant Details Modal (Host Only) -->
    <?php if ($is_host): ?>
    <div id="participantModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold" id="modalParticipantName">Participant Details</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="modalContent" class="space-y-4">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        // Pass PHP data to JavaScript
        const sessionId = <?= json_encode($session_id) ?>;
        const quizId = <?= json_encode($quiz_id) ?>;
        const userId = <?= json_encode($user_id) ?>;
        const hostId = <?= json_encode($host_id) ?>;
        const isHost = <?= $is_host ? 'true' : 'false' ?>;

        // Host view toggle
        document.getElementById('toggleHostView')?.addEventListener('click', () => {
            document.body.classList.toggle('host-view');
        });

        // Back button
        document.getElementById('backButton')?.addEventListener('click', () => {
            window.location.href = '../';
        });

        // Modal close button
        document.getElementById('closeModal')?.addEventListener('click', () => {
            document.getElementById('participantModal').classList.add('hidden');
        });

        <?php if ($is_host): ?>
        // Host-only function to show participant details
        async function showParticipantDetails(participantId) {
            const modal = document.getElementById('participantModal');
            const nameElement = document.getElementById('modalParticipantName');
            const contentElement = document.getElementById('modalContent');
            
            // Show loading state
            nameElement.textContent = 'Loading...';
            contentElement.innerHTML = '<div class="text-center py-4">Loading participant data...</div>';
            modal.classList.remove('hidden');
            
            try {
                const response = await fetch(`../backend/leaderboardBackend.php?ajax=1&user_id=${participantId}&session_id=${sessionId}&host_view=1`);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                
                // Build the modal content
                let html = `
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-gray-500 text-sm">Rank</div>
                            <div class="font-bold text-xl">${data.rank || 'N/A'}</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-gray-500 text-sm">Score</div>
                            <div class="font-bold text-xl">${data.score}/${data.totalQuestions}</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-gray-500 text-sm">Time Taken</div>
                            <div class="font-bold text-xl">${data.timeTaken || 'N/A'}</div>
                        </div>
                    </div>
                `;
                
                if (data.questionResults?.length > 0) {
                    html += `<h4 class="font-medium text-gray-700 mb-3">Question Breakdown</h4><div class="space-y-3">`;
                    
                    data.questionResults.forEach((q, i) => {
                        html += `
                            <div class="p-3 rounded-lg ${q.correct ? 'bg-green-50' : 'bg-red-50'}">
                                <div class="font-medium mb-2">Q${i+1}: ${q.question}</div>
                                <div class="text-sm mb-1 ${q.correct ? 'text-green-700' : 'text-red-700'}">
                                    Participant's answer: ${q.user_answer || 'No answer'}
                                </div>
                                ${!q.correct ? `
                                <div class="text-sm text-green-700">
                                    Correct answer: ${q.correct_answer}
                                </div>` : ''}
                                <div class="text-xs text-gray-500 mt-1">
                                    Time spent: ${q.timeSpent || 'N/A'}
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `</div>`;
                } else {
                    html += '<p class="text-gray-500">No question data available</p>';
                }
                
                nameElement.textContent = `${data.name}'s Performance`;
                contentElement.innerHTML = html;
            } catch (error) {
                console.error('Error:', error);
                contentElement.innerHTML = `
                    <div class="p-4 bg-red-100 text-red-700 rounded">
                        Error loading details: ${error.message}
                    </div>
                `;
            }
        }
        <?php endif; ?>
    </script>
</body>
</html>