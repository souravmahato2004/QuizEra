<!DOCTYPE html>
<html lang="en">
    <?php include'../backend/quizreadybackend.php' ?>
<head>
    <meta charset="UTF-8">
    <title><?php echo $is_participant ? 'Join Quiz' : 'Host Quiz'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
    <style>
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }

    .participant-list {
        height: calc(100vh - 300px);
        overflow-y: auto;
    }

    .participant-list::-webkit-scrollbar {
        width: 6px;
    }

    .participant-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .participant-list::-webkit-scrollbar-thumb {
        background: #A435F0;
        border-radius: 3px;
    }

    .participant-list::-webkit-scrollbar-thumb:hover {
        background: #8a2be2;
    }

    .code-display {
        letter-spacing: 0.2em;
        font-size: 2.5rem;
        text-shadow: 0 0 10px rgba(164, 53, 240, 0.3);
    }

    .timer-display {
        font-size: 2rem;
        font-family: monospace;
    }

    .timer-running {
        color: #A435F0;
    }

    .timer-warning {
        color: #F59E0B;
    }

    .timer-danger {
        color: #EF4444;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    .waiting-message {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }
    </style>
</head>


<body class="bg-[#E5E5E5] min-h-screen flex flex-col font-outfit">

    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
        <div class="flex items-center gap-2">
            <button onclick="window.location.href='mainPage.php'" class="top-10 left-10 text-black z-20">
                <i class="ri-arrow-left-long-line"></i>
            </button>
            <span class="text-lg font-small">
                <?php echo $is_participant ? 'Joining Quiz: ' : 'Host Quiz: '; ?>
                <?php echo htmlspecialchars($title); ?>
            </span>
        </div>

        <div class="flex items-center gap-3">
            <?php if (!$is_participant): ?>
            <div id="quizTimer" class="timer-display hidden bg-[#EFDAFE] px-4 py-1 rounded-lg">
                <span id="minutes">00</span>:<span id="seconds">00</span>
            </div>
            <?php endif; ?>
            <div class="relative inline-flex items-center gap-2">
                <div class="relative">
                    <img id="profileImage" src="../assets/profilepic/<?php echo $_SESSION['profile_pic'] ?? 'demo.jpg'; ?>" alt="Profile"
                        class="w-10 h-10 rounded-full object-cover cursor-pointer">
                </div>
                <button class="bg-gray-200 px-4 py-1 rounded-full text-lg hover:bg-[#CFCFCF]">Help</button>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <?php if ($is_participant): ?>
    <!-- Participant Interface -->
    <div class="flex flex-col items-center justify-center h-full p-8">
        <div class="waiting-message max-w-2xl w-full">
            <div class="pulse-animation mb-6">
                <i class="ri-user-heart-line text-6xl text-[#A435F0]"></i>
            </div>
            <h1 class="text-3xl font-bold text-[#A435F0] mb-4">Waiting for Host to Start</h1>
            <p class="text-gray-600 mb-6">You've successfully joined the quiz. The host will start the quiz shortly.</p>
            <div class="bg-[#EFDAFE] p-4 rounded-lg mb-6">
                <p class="text-sm text-gray-700 mb-2">Quiz Code:</p>
                <div class="code-display text-[#A435F0] font-bold text-center">
                    <?php echo htmlspecialchars($session_code); ?>
                </div>
            </div>
            <div class="flex justify-center">
                <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12"></div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Host Interface -->
    <div class="flex flex-row bg-[#E5E5E5] h-full mt-2 p-6">
        <!-- Left Panel - Quiz Code and Settings -->
        <div class="flex flex-col w-2/3 bg-white rounded-xl p-8 shadow-lg mr-6">
            <h1 class="text-2xl font-semibold mb-6 text-[#A435F0]">Host Your Quiz</h1>
            
            <!-- Quiz Code Section -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-2">Share this code with participants:</h2>
                <div class="flex items-center">
                    <div id="quizCode" class="code-display bg-[#EFDAFE] px-6 py-3 rounded-lg text-[#A435F0] font-bold">
                        <?php echo getCurrentQuizSession($_GET['id'], $conn); ?>
                    </div>
                    <button id="copyCodeBtn" class="ml-4 bg-[#A435F0] text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                        <i class="ri-file-copy-line mr-2"></i>Copy
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-2">Participants can join at quiz.example.com/join</p>
            </div>
            
            <!-- Quiz Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-4">Quiz Settings</h2>
                
                <div class="grid grid-cols-2 gap-6">
                    <!-- Timer Settings -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium mb-3 flex items-center">
                            <i class="ri-timer-line mr-2 text-[#A435F0]"></i>Timer Settings
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Total Quiz Time (minutes)</label>
                                <input type="number" id="quizDuration" min="1" max="120" value="15" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Host Controls -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <div class="flex space-x-3">
                    <button id="shareLinkBtn" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
                        <i class="ri-share-line mr-2"></i>Share Link
                    </button>
                </div>
                <div class="flex space-x-3" id="quizControls">
                    <button id="pauseQuizBtn" class="hidden bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                        <i class="ri-pause-line mr-2"></i>Pause
                    </button>
                    <button id="endQuizBtn" class="hidden bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="ri-stop-line mr-2"></i>End
                    </button>
                    <button id="startQuizBtn" class="bg-[#A435F0] text-white px-6 py-2 rounded-lg hover:bg-purple-700 text-lg">
                        <i class="ri-play-line mr-2"></i>Start Quiz
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Participants -->
        <div class="flex flex-col w-1/3 bg-white rounded-xl p-6 shadow-lg">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-[#A435F0]">Participants</h2>
                <span id="participantCount" class="bg-[#EFDAFE] text-[#A435F0] px-3 py-1 rounded-full text-sm">0</span>
            </div>
            
            <!-- Participant List -->
            <div class="participant-list mb-4">
                <div id="participantList" class="space-y-3">
                    <div class="text-center text-gray-500 py-10">
                        <i class="ri-user-3-line text-4xl mb-2"></i>
                        <p>Waiting for participants to join...</p>
                    </div>
                </div>
            </div>
            
            <!-- Participant Controls -->
            <div class="pt-4 border-t border-gray-200">
                <button id="refreshParticipants" class="text-[#A435F0] hover:text-purple-700 flex items-center">
                    <i class="ri-refresh-line mr-2"></i>Refresh List
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quiz Ended Modal -->
    <div id="quizEndedModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full text-center">
            <div class="text-6xl mb-4 text-[#A435F0]">
                <i class="ri-flag-2-fill"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Quiz Ended</h2>
            <p class="text-gray-600 mb-6">The quiz has been completed. You can now view the results.</p>
            <button id="viewResultsBtn" class="bg-[#A435F0] text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                View Results
            </button>
        </div>
    </div>

    <script>
        // Quiz state variables
        let quizDuration = 15; // in minutes
        let timeLeft = 0; // in seconds
        let timerInterval = null;
        let isQuizRunning = false;
        let isQuizPaused = false;
        let participantCount = 0;
        const quizCode = "<?php echo $is_participant ? $session_code : getCurrentQuizSession($_GET['id'], $conn); ?>";
        const isParticipant = <?php echo $is_participant ? 'true' : 'false'; ?>;
        let quizStatusCheckInterval = null;

        // DOM elements
        const quizTimer = document.getElementById('quizTimer');
        const minutesDisplay = document.getElementById('minutes');
        const secondsDisplay = document.getElementById('seconds');
        const quizDurationInput = document.getElementById('quizDuration');
        const startQuizBtn = document.getElementById('startQuizBtn');
        const pauseQuizBtn = document.getElementById('pauseQuizBtn');
        const endQuizBtn = document.getElementById('endQuizBtn');
        const quizControls = document.getElementById('quizControls');
        const quizEndedModal = document.getElementById('quizEndedModal');
        const viewResultsBtn = document.getElementById('viewResultsBtn');
        const shareLinkBtn = document.getElementById('shareLinkBtn');
        const refreshParticipantsBtn = document.getElementById('refreshParticipants');
        const participantList = document.getElementById('participantList');
        const participantCountDisplay = document.getElementById('participantCount');

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            if (!isParticipant) {
                refreshParticipants();
                // Set up periodic participant refresh
                setInterval(refreshParticipants, 5000);
            } else {
                // For participants, check quiz status periodically
                checkQuizStatus();
                quizStatusCheckInterval = setInterval(checkQuizStatus, 3000);
            }
        });

        // For participants - check if quiz has started
        async function checkQuizStatus() {
            try {
                const response = await fetch('../backend/quizreadybackend.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=check_quiz_status&session_code=${quizCode}`
                });
                
                const data = await response.json();
                
                if (data.status === 'success' && data.started) {
                    clearInterval(quizStatusCheckInterval);
                    window.location.href = `mainQuiz.php?session_code=${quizCode}`;
                }
            } catch (error) {
                console.error('Error checking quiz status:', error);
            }
        }

        // Copy code functionality
        if (document.getElementById('copyCodeBtn')) {
            document.getElementById('copyCodeBtn').addEventListener('click', function() {
                const code = document.getElementById('quizCode').textContent;
                navigator.clipboard.writeText(code).then(() => {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="ri-check-line mr-2"></i>Copied!';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                });
            });
        }

        // Share link functionality
        if (shareLinkBtn) {
            shareLinkBtn.addEventListener('click', function() {
                const code = document.getElementById('quizCode').textContent;
                const shareUrl = `${window.location.origin}/join/${code}`;
                
                if (navigator.share) {
                    navigator.share({
                        title: 'Join my quiz!',
                        text: `Use this code to join my quiz: ${code}`,
                        url: shareUrl
                    }).catch(err => {
                        console.log('Error sharing:', err);
                        copyToClipboard(shareUrl);
                    });
                } else {
                    copyToClipboard(shareUrl);
                }
                
                function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        alert('Link copied to clipboard!');
                    });
                }
            });
        }
        
        // Refresh participants list
        async function refreshParticipants() {
            try {
                const response = await fetch('../backend/quizreadybackend.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=get_participants&session_code=${quizCode}`
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    updateParticipantList(data.participants);
                } else {
                    console.error('Failed to fetch participants:', data.message);
                }
            } catch (error) {
                console.error('Error fetching participants:', error);
            }
        }
        
        // Update participant list in UI
        function updateParticipantList(participants) {
            participantList.innerHTML = '';
            participantCount = participants.length;
            participantCountDisplay.textContent = participantCount;
            
            if (participantCount === 0) {
                participantList.innerHTML = `
                    <div class="text-center text-gray-500 py-10">
                        <i class="ri-user-3-line text-4xl mb-2"></i>
                        <p>Waiting for participants to join...</p>
                    </div>
                `;
            } else {
                participants.forEach(participant => {
                    const participantElement = document.createElement('div');
                    participantElement.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                    participantElement.innerHTML = `
                        <div class="flex items-center">
                            <img src="${participant.profile_pic || '../assets/profilepic/default.jpg'}" 
                                 alt="${participant.username}" 
                                 class="w-8 h-8 rounded-full object-cover mr-3">
                            <span>${participant.username}</span>
                        </div>
                        <span class="text-xs text-gray-500">Joined</span>
                    `;
                    participantList.appendChild(participantElement);
                });
            }
        }
        
        // Start quiz button
        if (startQuizBtn) {
            startQuizBtn.addEventListener('click', async function() {
                if (participantCount === 0) {
                    alert('No participants have joined yet!');
                    return;
                }
                
                quizDuration = parseInt(quizDurationInput.value) || 15;
                timeLeft = quizDuration * 60; // Convert to seconds
                
                // Send request to server to start quiz with timer
                try {
                    const response = await fetch('../backend/quizreadybackend.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=start_quiz&session_code=${quizCode}&duration=${quizDuration}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        isQuizRunning = true;
                        updateTimerDisplay();
                        quizTimer.classList.remove('hidden');
                        quizTimer.classList.add('timer-running');
                        
                        // Show pause and end buttons
                        startQuizBtn.classList.add('hidden');
                        pauseQuizBtn.classList.remove('hidden');
                        endQuizBtn.classList.remove('hidden');
                        
                        // Start the timer
                        timerInterval = setInterval(updateTimer, 1000);
                    } else {
                        alert('Failed to start quiz: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error starting quiz:', error);
                    alert('Failed to start quiz');
                }
            });
        }

        // Pause quiz button
        if (pauseQuizBtn) {
            pauseQuizBtn.addEventListener('click', function() {
                if (isQuizPaused) {
                    resumeQuiz();
                } else {
                    pauseQuiz();
                }
            });
        }

        // End quiz button
        if (endQuizBtn) {
            endQuizBtn.addEventListener('click', async function() {
                clearInterval(timerInterval);
                
                try {
                    const response = await fetch('../backend/quizreadybackend.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=end_quiz&session_code=${quizCode}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        isQuizRunning = false;
                        isQuizPaused = false;
                        
                        // Reset UI
                        quizTimer.classList.add('hidden');
                        startQuizBtn.classList.remove('hidden');
                        pauseQuizBtn.classList.add('hidden');
                        endQuizBtn.classList.add('hidden');
                        
                        // Show quiz ended modal
                        quizEndedModal.classList.remove('hidden');
                    } else {
                        alert('Failed to end quiz: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error ending quiz:', error);
                    alert('Failed to end quiz');
                }
            });
        }

        // View results button
        if (viewResultsBtn) {
            viewResultsBtn.addEventListener('click', function() {
                // Redirect to results page with quiz code
                window.location.href = `results.php?session_code=${quizCode}`;
            });
        }

        // Timer functions
        function pauseQuiz() {
            if (!isQuizRunning || isQuizPaused) return;
            
            clearInterval(timerInterval);
            isQuizPaused = true;
            pauseQuizBtn.innerHTML = '<i class="ri-play-line mr-2"></i>Resume';
            quizTimer.classList.remove('timer-running', 'timer-warning', 'timer-danger');
            quizTimer.classList.add('text-gray-500');
        }

        function resumeQuiz() {
            if (!isQuizRunning || !isQuizPaused) return;
            
            isQuizPaused = false;
            pauseQuizBtn.innerHTML = '<i class="ri-pause-line mr-2"></i>Pause';
            quizTimer.classList.remove('text-gray-500');
            updateTimerClass();
            
            timerInterval = setInterval(updateTimer, 1000);
        }

        function updateTimer() {
            if (timeLeft <= 0) {
                endQuiz();
                return;
            }
            
            timeLeft--;
            updateTimerDisplay();
            updateTimerClass();
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            minutesDisplay.textContent = minutes.toString().padStart(2, '0');
            secondsDisplay.textContent = seconds.toString().padStart(2, '0');
        }

        function updateTimerClass() {
            // Change color based on remaining time
            const warningThreshold = 5 * 60; // 5 minutes
            const dangerThreshold = 1 * 60; // 1 minute
            
            quizTimer.classList.remove('timer-running', 'timer-warning', 'timer-danger');
            
            if (timeLeft <= dangerThreshold) {
                quizTimer.classList.add('timer-danger');
            } else if (timeLeft <= warningThreshold) {
                quizTimer.classList.add('timer-warning');
            } else {
                quizTimer.classList.add('timer-running');
            }
        }
    </script>
</body>
</html>