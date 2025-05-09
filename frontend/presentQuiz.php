<?php
session_start();
require_once '../backend/db.php'; // Database connection file

// Get session code from URL
$session_code = isset($_GET['session_code']) ? $_GET['session_code'] : null;

if (!$session_code) {
    die("Session code is required");
}

// Fetch session details
$session_query = "SELECT * FROM quiz_sessions WHERE code = ?";
$stmt = $conn->prepare($session_query);
$stmt->bind_param("s", $session_code);
$stmt->execute();
$session_result = $stmt->get_result();

if ($session_result->num_rows === 0) {
    die("Invalid session code");
}

$session = $session_result->fetch_assoc();
$quiz_id = $session['quiz_id'];
$timer_duration = $session['timer_duration'] ? $session['timer_duration'] * 60 : 0; // Convert minutes to seconds

// Fetch quiz details
$quiz_query = "SELECT * FROM quizzes WHERE quiz_id = ?";
$stmt = $conn->prepare($quiz_query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz_result = $stmt->get_result();
$quiz = $quiz_result->fetch_assoc();

// Fetch quiz slides
$slides_query = "SELECT * FROM quiz_slides WHERE quiz_id = ? ORDER BY position";
$stmt = $conn->prepare($slides_query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$slides_result = $stmt->get_result();
$slides = $slides_result->fetch_all(MYSQLI_ASSOC);

// Initialize user responses array
$user_responses = [];
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $responses_query = "SELECT slide_id, answer FROM quiz_responses WHERE quiz_id = ? AND user_id = ?";
    $stmt = $conn->prepare($responses_query);
    $stmt->bind_param("ii", $quiz_id, $user_id);
    $stmt->execute();
    $responses_result = $stmt->get_result();
    while ($row = $responses_result->fetch_assoc()) {
        $user_responses[$row['slide_id']] = $row['answer'];
    }
}

// Fetch participants for this session
$participants_query = "SELECT u.username, u.user_pic FROM quiz_participants p 
                      JOIN user_info u ON p.user_id = u.user_id 
                      WHERE p.session_id = ?";
$stmt = $conn->prepare($participants_query);
$stmt->bind_param("i", $session['id']);
$stmt->execute();
$participants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers'])) {
    $user_id = $_SESSION['id'];

    // Delete previous responses
    $delete_query = "DELETE FROM quiz_responses WHERE quiz_id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("ii", $quiz_id, $user_id);
    $stmt->execute();

    foreach ($_POST['answers'] as $slide_id => $answer) {
        // Determine if answer is correct
        $is_correct = 0;

        // Get slide type
        $slide_type_query = "SELECT question_type FROM quiz_slides WHERE slide_id = ?";
        $stmt = $conn->prepare($slide_type_query);
        $stmt->bind_param("i", $slide_id);
        $stmt->execute();
        $slide_type_result = $stmt->get_result();
        $slide_type = $slide_type_result->fetch_assoc()['question_type'];

        if ($slide_type === 'multiple') {
            // Check if selected option is correct
            $option_query = "SELECT is_correct FROM slide_options WHERE option_id = ?";
            $stmt = $conn->prepare($option_query);
            $stmt->bind_param("i", $answer);
            $stmt->execute();
            $option_result = $stmt->get_result();
            $is_correct = $option_result->fetch_assoc()['is_correct'];
        } elseif ($slide_type === 'fillblank') {
            // Check if answer matches correct answer
            $answer_query = "SELECT correct_answer FROM slide_answers WHERE slide_id = ?";
            $stmt = $conn->prepare($answer_query);
            $stmt->bind_param("i", $slide_id);
            $stmt->execute();
            $answer_result = $stmt->get_result();
            $correct_answer = $answer_result->fetch_assoc()['correct_answer'];

            $is_correct = strtolower(trim($answer)) === strtolower(trim($correct_answer)) ? 1 : 0;
        }

        // Save response
        $insert_query = "INSERT INTO quiz_responses (quiz_id, slide_id, user_id, answer, is_correct) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiisi", $quiz_id, $slide_id, $user_id, $answer, $is_correct);
        $stmt->execute();
    }

    // Calculate total score
    $score_query = "SELECT SUM(is_correct) as score FROM quiz_responses 
                    WHERE quiz_id = ? AND user_id = ?";
    $stmt = $conn->prepare($score_query);
    $stmt->bind_param("ii", $quiz_id, $user_id);
    $stmt->execute();
    $score_result = $stmt->get_result();
    $score = $score_result->fetch_assoc()['score'];

    // Update participant score
    $update_query = "UPDATE quiz_participants SET score = ? 
                     WHERE session_id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("iii", $score, $session['id'], $user_id);
    $stmt->execute();

    // Show success modal
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('successModal').classList.remove('hidden');
                setTimeout(function() {
                    window.location.href = 'leaderboard.php?session_code=$session_code';
                }, 3000);
            });
          </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Present Quiz: <?php echo htmlspecialchars($quiz['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
    <style>
        @media (max-width: 640px) {
            .grid-cols-2 {
                grid-template-columns: 1fr;
            }
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }

        .option:hover {
            background-color: #f1f5f9;
        }

        .option.selected {
            background-color: #e0e7ff;
            border-color: #6366f1;
        }

        .question-nav-btn {
            transition: all 0.2s ease;
            width: 36px;
            height: 36px;
            background-color: white;
            border: 1px solid #e2e8f0;
        }

        .question-nav-btn.answered {
            background-color: #10b981;
            color: white;
            border-color: transparent;
        }

        .question-nav-btn.visited {
            background-color: #ef4444;
            color: white;
            border-color: transparent;
        }

        .question-nav-btn.current {
            box-shadow: 0 0 0 2px #6366f1;
        }

        .timer-badge {
            background-color: #f1f5f9;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }

        .mobile-question-nav {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .participant-avatar {
            min-width: 32px;
            min-height: 32px;
            max-width: 32px;
            max-height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-question-nav::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-200">
    <!-- Header Section -->
    <header class="bg-white shadow-md py-4 px-4 sticky top-0 z-10">
        <div class="max-w-8xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="mainPage.php" class="text-gray-600 hover:text-indigo-600">
                    <i class="ri-arrow-left-line text-xl"></i>
                </a>
                <h1 class="text-2xl font-medium text-gray-800 truncate max-w-xs">Quiz: <?php echo htmlspecialchars($quiz['title']); ?></h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600 text-md bg-gray-100 px-4 py-[2px] rounded-full">Q<span id="currentQuestionNum"
                        class="font-medium">1</span>/<?php echo count($slides); ?></span>
                <?php if ($timer_duration > 0): ?>
                    <span class="timer-badge flex items-center text-gray-700">
                        <i class="ri-time-line mr-1"></i>
                        <span id="quizTimer" class="text-md"><?php echo gmdate("i:s", $timer_duration); ?></span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="max-w-8xl mx-auto p-4 flex flex-col lg:flex-row">
        <!-- Questions Navigation Sidebar (Left Column - Desktop) -->
        <div class="hidden lg:block w-16 mr-6 w-fit pt-3">
            <div class="bg-white rounded-xl shadow-sm p-4 sticky top-20 h-fit">
                <h3 class="font-semibold text-gray-800 mb-4 text-center text-sm">Questions</h3>
                <div class="space-y-2 flex flex-col items-center">
                    <?php foreach ($slides as $index => $slide): ?>
                        <button class="question-nav-btn rounded-full flex items-center justify-center
                            <?php echo isset($user_responses[$slide['slide_id']]) ? 'answered' : ''; ?>
                            <?php echo isset($_SESSION['visited_slides'][$slide['slide_id']]) && !isset($user_responses[$slide['slide_id']]) ? 'visited' : ''; ?>
                            <?php echo $index === 0 ? 'current' : ''; ?>" onclick="showSlide(<?php echo $index; ?>)"
                            data-slide-id="<?php echo $slide['slide_id']; ?>">
                            <?php echo $index + 1; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Questions Navigation (Mobile - Horizontal Scroll) -->
        <div class="lg:hidden bg-white rounded-lg shadow-sm p-3 mb-4 overflow-x-auto mobile-question-nav">
            <div class="flex space-x-2 w-max">
                <?php foreach ($slides as $index => $slide): ?>
                    <button class="question-nav-btn rounded-full flex-shrink-0 flex items-center justify-center
                        <?php echo isset($user_responses[$slide['slide_id']]) ? 'answered' : ''; ?>
                        <?php echo isset($_SESSION['visited_slides'][$slide['slide_id']]) && !isset($user_responses[$slide['slide_id']]) ? 'visited' : ''; ?>
                        <?php echo $index === 0 ? 'current' : ''; ?>" onclick="showSlide(<?php echo $index; ?>)"
                        data-slide-id="<?php echo $slide['slide_id']; ?>">
                        <?php echo $index + 1; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 mt-3">
            <form id="quizForm" method="POST" action="presentQuiz.php?session_code=<?php echo $session_code; ?>">
                <?php foreach ($slides as $index => $slide): ?>
                    <?php
                    // Fetch options for this slide if it's multiple choice
                    $options = [];
                    if ($slide['question_type'] === 'multiple') {
                        $options_query = "SELECT * FROM slide_options WHERE slide_id = ? ORDER BY position";
                        $stmt = $conn->prepare($options_query);
                        $stmt->bind_param("i", $slide['slide_id']);
                        $stmt->execute();
                        $options_result = $stmt->get_result();
                        $options = $options_result->fetch_all(MYSQLI_ASSOC);
                    }
                    ?>
                    <!-- questions displaying -->
                    <div class="slide-container min-h-[450px] <?php echo $index === 0 ? 'block' : 'hidden'; ?> bg-white rounded-xl shadow-sm p-6 mb-6"
                        id="slide-<?php echo $slide['slide_id']; ?>">
                        <div class="mb-4">
                            <span class="text-gray-500 text-sm"><?php echo ucfirst($slide['question_type']); ?>
                                question</span>
                        </div>

                        <h2 class="text-xl font-semibold text-gray-800 mb-6">
                            <?php echo htmlspecialchars($slide['question']); ?></h2>

                        <?php if ($slide['question_type'] === 'multiple'): ?>
                            <div class="grid grid-cols-2 gap-3">
                                <?php foreach ($options as $option): ?>
                                    <div class="option p-4 border border-gray-200 rounded-lg cursor-pointer transition-colors
                <?php echo (isset($user_responses[$slide['slide_id']]) && $user_responses[$slide['slide_id']] == $option['option_id']) ? 'selected' : ''; ?>"
                                        onclick="selectOption(this, <?php echo $option['option_id']; ?>)">
                                        <input type="radio" name="answers[<?php echo $slide['slide_id']; ?>]"
                                            value="<?php echo $option['option_id']; ?>" class="hidden"
                                            <?php echo (isset($user_responses[$slide['slide_id']]) && $user_responses[$slide['slide_id']] == $option['option_id']) ? 'checked' : ''; ?>>
                                        <span><?php echo htmlspecialchars($option['option_text']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif ($slide['question_type'] === 'fillblank'): ?>
                            <input type="text" name="answers[<?php echo $slide['slide_id']; ?>]"
                                placeholder="Type your answer here..."
                                value="<?php echo isset($user_responses[$slide['slide_id']]) ? htmlspecialchars($user_responses[$slide['slide_id']]) : ''; ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <?php elseif ($slide['question_type'] === 'shortanswer'): ?>
                            <textarea name="answers[<?php echo $slide['slide_id']; ?>]" placeholder="Type your answer here..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 min-h-[120px]"><?php echo isset($user_responses[$slide['slide_id']]) ? htmlspecialchars($user_responses[$slide['slide_id']]) : ''; ?></textarea>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="flex justify-between bg-white rounded-xl shadow-sm p-4">
                    <button type="button" onclick="navigateSlide(-1)"
                        class="px-6 py-2 text-gray-600 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="prevBtn" disabled>
                        <i class="ri-arrow-left-line mr-2"></i>Previous
                    </button>
                    <button type="button" onclick="navigateSlide(1)"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700" id="nextBtn">
                        Next<i class="ri-arrow-right-line ml-2"></i>
                    </button>
                    <button type="submit" class="hidden px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                        id="submitBtn">
                        Submit Quiz<i class="ri-send-plane-line ml-2"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Sidebar (Participants) -->
        <div class="hidden lg:block w-72 ml-6 pt-3">
            <div class="bg-white rounded-xl shadow-sm p-6 sticky top-20 h-fit">
                <h3 class="font-semibold text-gray-800 mb-4">Participants (<?php echo count($participants); ?>)</h3>
                <div class="space-y-3 max-h-[calc(100vh-180px)] overflow-y-auto">
                    <?php foreach ($participants as $participant): ?>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 mr-3 flex-shrink-0">
                                <?php if ($participant['user_pic']): ?>
                                    <img src="data:image/jpg;base64,<?php echo base64_encode($participant['user_pic']); ?>"
                                        alt="Profile" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-500">
                                        <i class="ri-user-line text-lg"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="truncate"><?php echo htmlspecialchars($participant['username']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 text-center">
            <div class="text-green-500 text-5xl mb-4">
                <i class="ri-checkbox-circle-fill"></i>
            </div>
            <h3 class="text-2xl font-semibold text-gray-800 mb-2">Quiz Submitted!</h3>
            <p class="text-gray-600 mb-6">Your answers have been saved. Redirecting to leaderboard...</p>
            <div class="flex justify-center">
                <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-indigo-500"></div>
            </div>
        </div>
    </div>

    <script>
        // Track visited slides
        if (!sessionStorage.getItem('visitedSlides')) {
            sessionStorage.setItem('visitedSlides', JSON.stringify({}));
        }

        let visitedSlides = JSON.parse(sessionStorage.getItem('visitedSlides'));
        let currentSlideIndex = 0;
        let totalSlides = <?php echo count($slides); ?>;
        let isSubmitted = false;

        <?php if ($timer_duration > 0): ?>
            // Timer functionality
            let timeLeft = <?php echo $timer_duration; ?>;
            const timerElement = document.getElementById('quizTimer');

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    if (!isSubmitted) {
                        isSubmitted = true;
                        document.getElementById('quizForm').submit();
                    }
                    return;
                }

                timeLeft--;
                setTimeout(updateTimer, 1000);
            }

            updateTimer();
        <?php endif; ?>

        // Option selection
        function selectOption(element, optionId) {
            // Remove selection from all options in this question
            const slideContainer = element.closest('.slide-container');
            slideContainer.querySelectorAll('.option').forEach(opt => {
                opt.classList.remove('selected');
            });

            // Add selection to clicked option
            element.classList.add('selected');

            // Update the radio input
            const radioInput = element.querySelector('input[type="radio"]');
            radioInput.checked = true;

            // Mark question as answered in navigation
            const slideId = slideContainer.id.split('-')[1];
            const navBtn = document.querySelector(`.question-nav-btn[data-slide-id="${slideId}"]`);
            if (navBtn) {
                navBtn.classList.add('answered');
                navBtn.classList.remove('visited');
            }
        }

        // Slide navigation
        function showSlide(index) {
            if (index < 0 || index >= totalSlides) return;

            // Update header counter
            document.getElementById('currentQuestionNum').textContent = index + 1;

            // Hide all slides
            document.querySelectorAll('.slide-container').forEach(slide => {
                slide.classList.add('hidden');
                slide.classList.remove('block');
            });

            // Show selected slide
            currentSlideIndex = index;
            const slides = document.querySelectorAll('.slide-container');
            slides[index].classList.remove('hidden');
            slides[index].classList.add('block');

            // Update navigation buttons
            document.getElementById('prevBtn').disabled = index === 0;
            document.getElementById('nextBtn').classList.toggle('hidden', index === totalSlides - 1);
            document.getElementById('submitBtn').classList.toggle('hidden', index !== totalSlides - 1);

            // Mark as visited
            const slideId = slides[index].id.split('-')[1];
            const slideIdNum = parseInt(slideId);
            visitedSlides[slideIdNum] = true;
            sessionStorage.setItem('visitedSlides', JSON.stringify(visitedSlides));

            // Update question navigation UI
            document.querySelectorAll('.question-nav-btn').forEach(btn => {
                btn.classList.remove('current');
            });

            const currentNavBtn = document.querySelector(`.question-nav-btn[data-slide-id="${slideId}"]`);
            if (currentNavBtn) {
                currentNavBtn.classList.add('current');

                // Mark as visited if not answered
                if (!currentNavBtn.classList.contains('answered')) {
                    currentNavBtn.classList.add('visited');
                }
            }

            // Scroll mobile navigation to show current question
            if (window.innerWidth < 1024) { // LG breakpoint
                const mobileNav = document.querySelector('.mobile-question-nav');
                const btn = mobileNav.querySelector(`.question-nav-btn[data-slide-id="${slideId}"]`);
                if (btn) {
                    mobileNav.scrollLeft = btn.offsetLeft - (mobileNav.offsetWidth / 2) + (btn.offsetWidth / 2);
                }
            }
        }

        function navigateSlide(offset) {
            showSlide(currentSlideIndex + offset);
        }

        // Initialize the view
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any already selected options
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                radio.closest('.option').classList.add('selected');
            });

            // Initialize visited slides from session storage
            Object.keys(visitedSlides).forEach(slideId => {
                const navBtn = document.querySelector(`.question-nav-btn[data-slide-id="${slideId}"]`);
                if (navBtn && !navBtn.classList.contains('answered')) {
                    navBtn.classList.add('visited');
                }
            });

            // Set up form submission
            document.getElementById('quizForm').addEventListener('submit', function(e) {
                if (isSubmitted) {
                    e.preventDefault();
                    return;
                }
                isSubmitted = true;
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').innerHTML =
                    '<i class="ri-loader-4-line animate-spin mr-2"></i>Submitting...';
            });
        });
    </script>
</body>

</html>