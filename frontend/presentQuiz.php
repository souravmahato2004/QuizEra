<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Play Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <style>
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
    
    .option-btn {
        transition: all 0.2s;
    }
    
    .option-btn.selected {
        transform: scale(1.02);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .option-btn.correct {
        animation: pulse-correct 1.5s infinite;
    }
    
    .option-btn.incorrect {
        animation: pulse-incorrect 1.5s infinite;
    }
    
    @keyframes pulse-correct {
        0%, 100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(74, 222, 128, 0); }
    }
    
    @keyframes pulse-incorrect {
        0%, 100% { box-shadow: 0 0 0 0 rgba(248, 113, 113, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(248, 113, 113, 0); }
    }
    
    .question-nav-btn {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        cursor: pointer;
    }
    
    .question-nav-btn.unvisited {
        background-color: #FEF9C3;
        border: 2px solid #FEF08A;
    }
    
    .question-nav-btn.visited {
        background-color: #FECACA;
        border: 2px solid #FCA5A5;
    }
    
    .question-nav-btn.attempted {
        background-color: #BBF7D0;
        border: 2px solid #86EFAC;
    }
    
    .question-nav-btn.current {
        transform: scale(1.1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    </style>
</head>

<body class="bg-[#E5E5E5] min-h-screen flex flex-col font-outfit">
    <!-- Quiz Header -->
    <div class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button onclick="window.history.back()" class="text-gray-600 hover:text-purple-700">
                <i class="ri-arrow-left-line text-2xl"></i>
            </button>
            <h1 class="text-xl font-bold text-gray-800">Quiz: <span id="quizTitle">NameOfQuiz</span></h1>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full">
                <i class="ri-user-line text-purple-700"></i>
                <span id="participantCount">12</span>
            </div>
            <div class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full">
                <i class="ri-question-line text-purple-700"></i>
                <span id="questionCount">1/10</span>
            </div>
            <button id="overallTimerBtn" class="flex items-center space-x-2 bg-red-100 px-3 py-1 rounded-full">
                <i class="ri-time-line text-red-700"></i>
                <span id="overallTimerDisplay">15:00</span>
            </button>
        </div>
    </div>

    <!-- Main Quiz Area -->
    <div class="flex-1 flex p-4 gap-4">
        <!-- Question Navigation Sidebar -->
        <div class="w-16 bg-white rounded-xl shadow-lg p-4 flex flex-col items-center px-10">
            <h3 class="text-sm font-bold mb-4">Questions</h3>
            <div class="space-y-2" id="questionNavigation">
                <!-- Question navigation buttons will be injected here -->
            </div>
        </div>
        
        <!-- Quiz Content -->
        <div class="flex-1 bg-white rounded-xl shadow-lg p-8 flex flex-col">
            <!-- Question Display -->
            <div class="flex-1 flex flex-col justify-center">
                <div id="questionContainer" class="mb-8">
                    <h2 class="text-3xl font-bold text-center mb-6" id="questionText">What is the capital of France?</h2>
                    
                    <!-- For text/image questions -->
                    <div id="questionMedia" class="flex justify-center mb-8">
                        <img src="https://via.placeholder.com/600x300" alt="Question Image" class="max-h-64 rounded-lg hidden" id="questionImage">
                    </div>
                    
                    <!-- Options Grid -->
                    <div id="optionsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Options will be injected here by JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- Navigation Controls -->
            <div class="flex justify-between mt-6">
                <button id="prevBtn" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="ri-arrow-left-line"></i> Previous
                </button>
                <button id="nextBtn" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                    Next <i class="ri-arrow-right-line"></i>
                </button>
                <button id="submitBtn" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 hidden">
                    Submit Quiz
                </button>
            </div>
        </div>
        
        <!-- Participants Sidebar -->
        <div class="w-80 bg-white rounded-xl shadow-lg p-4">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="ri-group-line mr-2"></i> Participants
            </h3>
            <div class="space-y-2 max-h-[calc(100vh-200px)] overflow-y-auto" id="participantsList">
                <!-- Participants will be injected here -->
            </div>
        </div>
    </div>

    <!-- Submission Modal -->
    <div id="submissionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full">
            <div class="text-center mb-6">
                <i class="ri-checkbox-circle-fill text-6xl text-green-500 mb-4"></i>
                <h2 class="text-2xl font-bold mb-2">Quiz Submitted!</h2>
                <p class="text-gray-600">Your score: <span class="font-bold text-purple-700" id="modalScore">0</span>/<span id="modalTotalQuestions">0</span></p>
                <p class="mt-4">Redirecting to leaderboard in <span id="countdown">3</span> seconds...</p>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../assets/js/presentQuiz.js"></script>
</body>
</html>