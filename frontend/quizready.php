<!DOCTYPE html>
<html lang="en">
    <?php include'../backend/quizreadybackend.php';?>
<head>
    <meta charset="UTF-8">
    <title>Host Quiz</title>
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
    </style>
</head>

<body class="bg-[#E5E5E5] min-h-screen flex flex-col font-outfit">

    <!-- Header (same as original) -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
        <div class="flex items-center gap-2">
            <button onclick="window.location.href='mainPage.php'" class=" top-10 left-10 text-black z-20">
                <i class="ri-arrow-left-long-line"></i>
            </button>
            <span class="text-lg font-small">Host Quiz: <?php echo htmlspecialchars($title);?></span>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative inline-flex items-center gap-2">
                <div class="relative">
                    <img id="profileImage" src="../assets/profilepic/demo.jpg" alt="Profile"
                        class="w-10 h-10 rounded-full object-cover cursor-pointer">
                </div>
                <button class="bg-gray-200 px-4 py-1 rounded-full text-lg hover:bg-[#CFCFCF]">Help</button>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-row bg-[#E5E5E5] h-full mt-2 p-6">
        <!-- Left Panel - Quiz Code and Settings -->
        <div class="flex flex-col w-2/3 bg-white rounded-xl p-8 shadow-lg mr-6">
            <h1 class="text-2xl font-semibold mb-6 text-[#A435F0]">Host Your Quiz</h1>
            
            <!-- Quiz Code Section -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-2">Share this code with participants:</h2>
                <div class="flex items-center">
                    <div id="quizCode" class="code-display bg-[#EFDAFE] px-6 py-3 rounded-lg text-[#A435F0] font-bold">
                        <?php echo generateQuizCode(); ?>
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
                                <input type="number" min="1" max="120" value="15" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Host Controls -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <div class="flex space-x-3">
                    <button class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
                        <i class="ri-share-line mr-2"></i>Share Link
                    </button>
                </div>
                <button id="startQuizBtn" class="bg-[#A435F0] text-white px-6 py-2 rounded-lg hover:bg-purple-700 text-lg">
                    <i class="ri-play-line mr-2"></i>Start Quiz
                </button>
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
                    <!-- Participants will appear here dynamically -->
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

    <script>
        // Generate a 6-digit random code
        function generateQuizCode() {
            return Math.floor(100000 + Math.random() * 900000);
        }
        
        // Display the generated code
        document.getElementById('quizCode').textContent = generateQuizCode();
        
        // Copy code functionality
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
        
        // Simulate participants joining (for demo)
        const participantNames = ['Alex Johnson', 'Sam Wilson', 'Taylor Swift', 'Jamie Smith', 'Casey Brown'];
        let participantCount = 0;
        
        document.getElementById('refreshParticipants').addEventListener('click', function() {
            const participantList = document.getElementById('participantList');
            participantList.innerHTML = '';
            
            // Add random participants for demo
            const randomCount = Math.floor(Math.random() * 5) + 1;
            participantCount = randomCount;
            document.getElementById('participantCount').textContent = participantCount;
            
            if (participantCount === 0) {
                participantList.innerHTML = `
                    <div class="text-center text-gray-500 py-10">
                        <i class="ri-user-3-line text-4xl mb-2"></i>
                        <p>Waiting for participants to join...</p>
                    </div>
                `;
            } else {
                for (let i = 0; i < participantCount; i++) {
                    const participant = document.createElement('div');
                    participant.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                    participant.innerHTML = `
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <i class="ri-user-3-line text-[#A435F0]"></i>
                            </div>
                            <span>${participantNames[i]}</span>
                        </div>
                        <span class="text-xs text-gray-500">Joined</span>
                    `;
                    participantList.appendChild(participant);
                }
            }
        });
        
        // Start quiz button
        document.getElementById('startQuizBtn').addEventListener('click', function() {
            if (participantCount === 0) {
                alert('No participants have joined yet!');
                return;
            }
            alert('Quiz is starting!');
            // In a real app, this would redirect to the quiz presentation page
        });
    </script>
</body>
</html>