<?php
session_start();
require_once '../backend/db.php';

// if (!isset($_SESSION['id'])) {
//     header("Location: signup.php");
//     exit;
// }

$userId = $_SESSION['id'];
$sessionCode = isset($_GET['session_code']) ? $_GET['session_code'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <style>
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .option-btn { transition: all 0.2s; }
    .option-btn.selected { transform: scale(1.02); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .option-btn.correct { animation: pulse-correct 1.5s infinite; }
    .option-btn.incorrect { animation: pulse-incorrect 1.5s infinite; }
    @keyframes pulse-correct {
        0%, 100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(74, 222, 128, 0); }
    }
    @keyframes pulse-incorrect {
        0%, 100% { box-shadow: 0 0 0 0 rgba(248, 113, 113, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(248, 113, 113, 0); }
    }
    .question-nav-btn {
        width: 2.5rem; height: 2.5rem; border-radius: 9999px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; cursor: pointer;
    }
    .question-nav-btn.unvisited { background-color: #FEF9C3; border: 2px solid #FEF08A; }
    .question-nav-btn.visited { background-color: #FECACA; border: 2px solid #FCA5A5; }
    .question-nav-btn.attempted { background-color: #BBF7D0; border: 2px solid #86EFAC; }
    .question-nav-btn.current { transform: scale(1.1); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    </style>
</head>
<body class="bg-[#E5E5E5] min-h-screen flex flex-col font-outfit" 
      data-session-code="<?= htmlspecialchars($sessionCode) ?>"
      data-user-id="<?= $userId ?>">
    
    <!-- Quiz Header -->
    <div class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button onclick="window.history.back()" class="text-gray-600 hover:text-purple-700">
                <i class="ri-arrow-left-line text-2xl"></i>
            </button>
            <h1 class="text-xl font-bold text-gray-800">Quiz: <span id="quizTitle">Loading...</span></h1>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full">
                <i class="ri-user-line text-purple-700"></i>
                <span id="participantCount">0</span>
            </div>
            <div class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full">
                <i class="ri-question-line text-purple-700"></i>
                <span id="questionCount">0/0</span>
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
            <div class="space-y-2" id="questionNavigation"></div>
        </div>
        
        <!-- Quiz Content -->
        <div class="flex-1 bg-white rounded-xl shadow-lg p-8 flex flex-col">
            <!-- Question Display -->
            <div class="flex-1 flex flex-col justify-center">
                <div id="questionContainer" class="mb-8">
                    <h2 class="text-3xl font-bold text-center mb-6" id="questionText">Loading quiz...</h2>
                    <div id="questionMedia" class="flex justify-center mb-8">
                        <img src="" alt="Question Image" class="max-h-64 rounded-lg hidden" id="questionImage">
                    </div>
                    <div id="optionsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
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
            <div class="space-y-2 max-h-[calc(100vh-200px)] overflow-y-auto" id="participantsList"></div>
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

    <script>
    document.addEventListener("DOMContentLoaded", async () => {
        // DOM Elements
        const elements = {
            quizTitle: document.getElementById("quizTitle"),
            questionText: document.getElementById("questionText"),
            questionImage: document.getElementById("questionImage"),
            optionsContainer: document.getElementById("optionsContainer"),
            questionCount: document.getElementById("questionCount"),
            participantCount: document.getElementById("participantCount"),
            overallTimerDisplay: document.getElementById("overallTimerDisplay"),
            prevBtn: document.getElementById("prevBtn"),
            nextBtn: document.getElementById("nextBtn"),
            submitBtn: document.getElementById("submitBtn"),
            questionNavigation: document.getElementById("questionNavigation"),
            submissionModal: document.getElementById("submissionModal"),
            modalScore: document.getElementById("modalScore"),
            modalTotalQuestions: document.getElementById("modalTotalQuestions"),
            countdown: document.getElementById("countdown"),
            participantsList: document.getElementById("participantsList")
        };

        // Get session data
        const sessionCode = document.body.dataset.sessionCode;
        const userId = document.body.dataset.userId;

        if (!sessionCode) {
            alert('No session code provided!');
            window.location.href = '/';
            return;
        }

        if (!userId) {
            alert('Please login to participate!');
            window.location.href = '/signup.php';
            return;
        }

        // Quiz State
        let quizData = {
            title: "",
            duration: 900, // 15 minutes default
            questions: [],
            participants: []
        };
        let currentQuestionIndex = 0;
        let userAnswers = [];
        let visitedQuestions = [];
        let overallTimer;
        let quizEndTime;
        let redirectTimer;

        // Initialize Quiz
        async function initQuiz() {
            try {
                // Fetch quiz data
                const response = await fetch(`/api.php?session_code=${sessionCode}`);
                if (!response.ok) throw new Error('Failed to fetch quiz data');
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to load quiz');
                }
                
                // Initialize quiz state
                quizData = {
                    title: data.quiz.title,
                    duration: data.quiz.duration || 900,
                    questions: data.questions.map(q => ({
                        slide_id: q.slide_id,
                        text: q.text,
                        type: q.type,
                        options: q.options || [],
                        correctAnswer: q.correct_answer,
                        image: q.image
                    })),
                    participants: data.participants || []
                };

                userAnswers = Array(quizData.questions.length).fill(null);
                visitedQuestions = Array(quizData.questions.length).fill(false);
                
                // Update UI
                updateUI();
                loadQuestion(currentQuestionIndex);
                startOverallTimer(quizData.duration);
                
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to load quiz. Please try again.');
                window.location.href = '/';
            }
        }

        function updateUI() {
            elements.quizTitle.textContent = quizData.title;
            elements.participantCount.textContent = quizData.participants.length;
            elements.modalTotalQuestions.textContent = quizData.questions.length;
            updateQuestionCount();
            renderQuestionNavigation();
            renderParticipants();
            
            if (quizData.questions.length <= 1) {
                elements.submitBtn.classList.remove('hidden');
                elements.nextBtn.classList.add('hidden');
            }
        }

        function loadQuestion(index) {
            if (index < 0 || index >= quizData.questions.length) return;
            
            currentQuestionIndex = index;
            visitedQuestions[index] = true;
            const question = quizData.questions[index];
            
            // Update question text
            elements.questionText.textContent = question.text;
            
            // Handle image
            if (question.image) {
                elements.questionImage.src = question.image;
                elements.questionImage.classList.remove('hidden');
            } else {
                elements.questionImage.classList.add('hidden');
            }
            
            // Clear previous options
            elements.optionsContainer.innerHTML = '';
            
            // Handle question types
            if (question.type === 'multiple') {
                question.options.forEach((option, i) => {
                    const optionBtn = document.createElement('button');
                    optionBtn.className = `option-btn bg-gray-100 text-black text-lg font-medium py-4 px-4 rounded-lg hover:bg-opacity-80 transition-all`;
                    optionBtn.textContent = option.text;
                    optionBtn.onclick = () => selectAnswer(i);
                    
                    if (userAnswers[index] === i) {
                        optionBtn.classList.add('selected');
                    }
                    
                    elements.optionsContainer.appendChild(optionBtn);
                });
            } else {
                // Text input for fillblank/shortanswer
                const input = document.createElement('input');
                input.type = 'text';
                input.className = 'border border-gray-300 rounded-lg px-4 py-3 w-full';
                input.placeholder = 'Type your answer...';
                input.value = userAnswers[index] || '';
                input.oninput = (e) => {
                    userAnswers[index] = e.target.value;
                    updateQuestionNavigation();
                };
                elements.optionsContainer.appendChild(input);
            }
            
            updateNavigationButtons();
            updateQuestionNavigation();
        }

        function selectAnswer(optionIndex) {
            userAnswers[currentQuestionIndex] = optionIndex;
            document.querySelectorAll('.option-btn').forEach((btn, i) => {
                btn.classList.toggle('selected', i === optionIndex);
            });
            updateQuestionNavigation();
        }

        function updateNavigationButtons() {
            elements.prevBtn.disabled = currentQuestionIndex === 0;
            
            const isLastQuestion = currentQuestionIndex === quizData.questions.length - 1;
            elements.nextBtn.classList.toggle('hidden', isLastQuestion);
            elements.submitBtn.classList.toggle('hidden', !isLastQuestion);
            
            updateQuestionCount();
        }

        function renderQuestionNavigation() {
            elements.questionNavigation.innerHTML = '';
            quizData.questions.forEach((_, i) => {
                const btn = document.createElement('button');
                btn.className = `question-nav-btn mb-2 ${getQuestionStateClass(i)}`;
                btn.textContent = i + 1;
                btn.onclick = () => navigateToQuestion(i);
                elements.questionNavigation.appendChild(btn);
            });
        }

        function getQuestionStateClass(index) {
            if (userAnswers[index] !== null) return 'attempted';
            if (visitedQuestions[index]) return 'visited';
            if (index === currentQuestionIndex) return 'current';
            return 'unvisited';
        }

        function navigateToQuestion(index) {
            if (index >= 0 && index < quizData.questions.length) {
                currentQuestionIndex = index;
                loadQuestion(index);
            }
        }

        function renderParticipants() {
            elements.participantsList.innerHTML = quizData.participants.map(p => `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold mr-2">
                            ${p.avatar}
                        </div>
                        <span>${p.name}</span>
                    </div>
                    <span class="text-sm ${p.hasAnswered ? 'text-green-600' : 'text-gray-400'}">
                        ${p.hasAnswered ? '✓' : '...'}
                    </span>
                </div>
            `).join('');
        }

        function startOverallTimer(seconds) {
            quizEndTime = Date.now() + seconds * 1000;
            updateTimerDisplay();
            
            overallTimer = setInterval(() => {
                updateTimerDisplay();
                if (quizEndTime <= Date.now()) {
                    clearInterval(overallTimer);
                    submitQuiz();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const remaining = Math.max(0, Math.floor((quizEndTime - Date.now()) / 1000));
            const mins = Math.floor(remaining / 60);
            const secs = remaining % 60;
            elements.overallTimerDisplay.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
            
            if (remaining <= 60) {
                elements.overallTimerBtn.classList.replace('bg-red-100', 'bg-red-500');
                elements.overallTimerBtn.classList.replace('text-red-700', 'text-white');
            }
        }

        function updateQuestionCount() {
            elements.questionCount.textContent = `${currentQuestionIndex + 1}/${quizData.questions.length}`;
        }

        async function submitQuiz() {
            clearInterval(overallTimer);
            
            try {
                const responses = quizData.questions.map((q, i) => ({
                    slide_id: q.slide_id,
                    answer: userAnswers[i] !== null ? userAnswers[i].toString() : ''
                })).filter(r => r.answer !== '');
                
                const response = await fetch(`api.php?session_code=${sessionCode}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ responses })
                });
                
                if (!response.ok) throw new Error('Submission failed');
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.error || 'Submission failed');
                }
                
                showSubmissionResult(result);
                
            } catch (error) {
                console.error('Submission error:', error);
                alert(error.message || 'Submission failed. Please try again.');
            }
        }

        function showSubmissionResult(result) {
            elements.modalScore.textContent = result.score;
            elements.submissionModal.classList.remove('hidden');
            
            let countdown = 3;
            elements.countdown.textContent = countdown;
            
            redirectTimer = setInterval(() => {
                countdown--;
                elements.countdown.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(redirectTimer);
                    window.location.href = `leaderboard.php?session_code=${sessionCode}`;
                }
            }, 1000);
        }

        // Event listeners
        elements.nextBtn.addEventListener('click', () => {
            currentQuestionIndex++;
            loadQuestion(currentQuestionIndex);
        });
        
        elements.prevBtn.addEventListener('click', () => {
            currentQuestionIndex--;
            loadQuestion(currentQuestionIndex);
        });
        
        elements.submitBtn.addEventListener('click', submitQuiz);

        // Initialize
        initQuiz();
    });
    </script>
</body>
</html>