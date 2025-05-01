document.addEventListener("DOMContentLoaded", () => {
    // Quiz Data - This would normally come from your backend/API
    const quizData = {
        title: "World Capitals",
        duration: 15 * 60, // 15 minutes in seconds
        questions: [
            {
                text: "What is the capital of France?",
                type: "multiple",
                options: ["London", "Berlin", "Paris", "Madrid"],
                correctAnswer: 2,
                image: null,
                optionColors: ["red-500", "yellow-400", "green-500", "blue-500"]
            },
            {
                text: "Type the capital of Japan:",
                type: "one-word",
                correctAnswer: "tokyo",
                image: null
            },
            {
                text: "What is the capital of Germany?",
                type: "multiple",
                options: ["Munich", "Hamburg", "Berlin", "Frankfurt"],
                correctAnswer: 2,
                image: null,
                optionColors: ["red-500", "yellow-400", "green-500", "blue-500"]
            }
        ],
        participants: [
            { id: 1, name: "JohnDoe", score: 0, avatar: "J", hasAnswered: true },
            { id: 2, name: "JaneSmith", score: 0, avatar: "J", hasAnswered: false },
            { id: 3, name: "BobJohnson", score: 0, avatar: "B", hasAnswered: true },
        ]
    };

    // DOM Elements
    const dom = {
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

    // Quiz State
    let currentQuestionIndex = 0;
    let userAnswers = Array(quizData.questions.length).fill(null);
    let visitedQuestions = Array(quizData.questions.length).fill(false);
    let overallTimer;
    let quizEndTime;
    let redirectTimer;
    let countdown = 3;

    // Initialize Quiz
    function initQuiz() {
        dom.quizTitle.textContent = quizData.title;
        dom.participantCount.textContent = quizData.participants.length;
        dom.modalTotalQuestions.textContent = quizData.questions.length;
        updateQuestionCount();
        loadQuestion(currentQuestionIndex);
        renderQuestionNavigation();
        renderParticipants();
        
        // Start overall quiz timer
        startOverallTimer(quizData.duration);
        
        // Show submit button on last question
        if (quizData.questions.length === 1) {
            dom.submitBtn.classList.remove('hidden');
            dom.nextBtn.classList.add('hidden');
        }
    }

    // Load Question
    function loadQuestion(index) {
        visitedQuestions[index] = true;
        const question = quizData.questions[index];
        dom.questionText.textContent = question.text;
        
        // Clear previous options
        dom.optionsContainer.innerHTML = '';
        
        // Show/hide image
        if (question.image) {
            dom.questionImage.src = question.image;
            dom.questionImage.classList.remove('hidden');
        } else {
            dom.questionImage.classList.add('hidden');
        }
        
        // Handle different question types
        if (question.type === "multiple") {
            // Create option buttons with colors
            question.options.forEach((option, i) => {
                const optionBtn = document.createElement('button');
                const colorClass = question.optionColors ? `bg-${question.optionColors[i]}` : 'bg-gray-100';
                optionBtn.className = `option-btn ${colorClass} text-black text-lg font-medium py-4 px-4 rounded-lg hover:bg-opacity-80 transition-all`;
                optionBtn.innerHTML = option;
                optionBtn.dataset.index = i;
                
                // Mark selected answer if exists
                if (userAnswers[index] !== null && userAnswers[index] === i) {
                    optionBtn.classList.add('selected');
                }
                
                optionBtn.addEventListener('click', () => selectAnswer(i));
                dom.optionsContainer.appendChild(optionBtn);
            });
        } 
        else if (question.type === "one-word") {
            // Create input for one-word answer
            const inputContainer = document.createElement('div');
            inputContainer.className = 'flex flex-col space-y-2 w-full';
            
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500';
            input.placeholder = 'Type your answer here...';
            
            // Pre-fill if already answered
            if (userAnswers[index] !== null) {
                input.value = userAnswers[index];
            }
            
            // Handle input changes
            input.addEventListener('input', (e) => {
                userAnswers[index] = e.target.value.trim();
                updateQuestionNavigation();
            });
            
            inputContainer.appendChild(input);
            dom.optionsContainer.appendChild(inputContainer);
        }
        
        // Update navigation buttons
        updateQuestionNavigation();
        
        // Update prev/next buttons
        dom.prevBtn.disabled = index === 0;
        
        if (index === quizData.questions.length - 1) {
            dom.nextBtn.classList.add('hidden');
            dom.submitBtn.classList.remove('hidden');
        } else {
            dom.nextBtn.classList.remove('hidden');
            dom.submitBtn.classList.add('hidden');
        }
        
        updateQuestionCount();
    }

    // Select Answer
    function selectAnswer(optionIndex) {
        userAnswers[currentQuestionIndex] = optionIndex;
        
        // Highlight selected option
        document.querySelectorAll('.option-btn').forEach((btn, i) => {
            btn.classList.remove('selected');
            if (i === optionIndex) {
                btn.classList.add('selected');
            }
        });
        
        // Update navigation to show attempted
        updateQuestionNavigation();
    }

    // Question Navigation
    function renderQuestionNavigation() {
        dom.questionNavigation.innerHTML = '';
        quizData.questions.forEach((_, index) => {
            const btn = document.createElement('button');
            
            // Set base classes
            btn.className = `question-nav-btn mb-2`;
            
            // Set state classes
            if (userAnswers[index] !== null) {
                btn.classList.add('attempted');
            } else if (visitedQuestions[index]) {
                btn.classList.add('visited');
            } else {
                btn.classList.add('unvisited');
            }
            
            // Highlight current question
            if (index === currentQuestionIndex) {
                btn.classList.add('current');
            }
            
            btn.textContent = index + 1;
            btn.dataset.index = index;
            
            btn.addEventListener('click', () => navigateToQuestion(index));
            dom.questionNavigation.appendChild(btn);
        });
    }
    
    function updateQuestionNavigation() {
        document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
            // Remove all state classes
            btn.classList.remove('unvisited', 'visited', 'attempted', 'current');
            
            // Add appropriate state class
            if (userAnswers[index] !== null) {
                btn.classList.add('attempted');
            } else if (visitedQuestions[index]) {
                btn.classList.add('visited');
            } else {
                btn.classList.add('unvisited');
            }
            
            // Highlight current question
            if (index === currentQuestionIndex) {
                btn.classList.add('current');
            }
        });
    }
    
    function navigateToQuestion(index) {
        currentQuestionIndex = index;
        loadQuestion(currentQuestionIndex);
    }

    // Participants List
    function renderParticipants() {
        dom.participantsList.innerHTML = quizData.participants.map(participant => `
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold mr-2">
                        ${participant.avatar}
                    </div>
                    <span>${participant.name}</span>
                </div>
                <span class="text-sm ${participant.hasAnswered ? 'text-green-600' : 'text-gray-400'}">
                    ${participant.hasAnswered ? '✓' : '...'}
                </span>
            </div>
        `).join('');
    }

    // Timer Functions
    function startOverallTimer(seconds) {
        quizEndTime = Date.now() + seconds * 1000;
        updateOverallTimerDisplay();
        
        overallTimer = setInterval(() => {
            updateOverallTimerDisplay();
            
            const timeRemaining = Math.max(0, Math.floor((quizEndTime - Date.now()) / 1000));
            
            if (timeRemaining <= 0) {
                clearInterval(overallTimer);
                submitQuiz();
            }
        }, 1000);
    }
    
    function updateOverallTimerDisplay() {
        const timeRemaining = Math.max(0, Math.floor((quizEndTime - Date.now()) / 1000));
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        dom.overallTimerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        // Change color when time is running out
        if (timeRemaining <= 60) {
            dom.overallTimerBtn.classList.remove('bg-red-100', 'text-red-700');
            dom.overallTimerBtn.classList.add('bg-red-500', 'text-white');
        }
    }

    // Navigation
    function nextQuestion() {
        if (currentQuestionIndex < quizData.questions.length - 1) {
            currentQuestionIndex++;
            loadQuestion(currentQuestionIndex);
        }
    }

    function prevQuestion() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            loadQuestion(currentQuestionIndex);
        }
    }

    // Quiz Submission
    function submitQuiz() {
        clearInterval(overallTimer);
        
        // Show submission modal
        dom.submissionModal.classList.remove('hidden');
        
        // Start countdown for redirect
        redirectTimer = setInterval(() => {
            countdown--;
            dom.countdown.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(redirectTimer);
                // In a real app, this would redirect to the leaderboard page
                alert("Quiz submitted successfully!");
            }
        }, 1000);
    }

    // UI Updates
    function updateQuestionCount() {
        dom.questionCount.textContent = `${currentQuestionIndex + 1}/${quizData.questions.length}`;
    }

    // Event Listeners
    dom.nextBtn.addEventListener('click', nextQuestion);
    dom.prevBtn.addEventListener('click', prevQuestion);
    dom.submitBtn.addEventListener('click', submitQuiz);

    // Initialize
    initQuiz();
});