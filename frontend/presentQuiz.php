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
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $responses_query = "SELECT slide_id, answer FROM quiz_responses WHERE quiz_id = ? AND user_id = ?";
    $stmt = $conn->prepare($responses_query);
    $stmt->bind_param("ii", $quiz_id, $user_id);
    $stmt->execute();
    $responses_result = $stmt->get_result();
    while ($row = $responses_result->fetch_assoc()) {
        $user_responses[$row['slide_id']] = $row['answer'];
    }
}

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
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap"rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
</head>
<body class="bg-gray-100">
    <!-- Header Section -->
    <header class="bg-indigo-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-2">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($quiz['title']); ?></h1>
                    <p class="text-indigo-100"><?php echo htmlspecialchars($quiz['description']); ?></p>
                </div>
                <?php if ($timer_duration > 0): ?>
                <div class="flex items-center bg-yellow-400 text-gray-800 px-4 py-2 rounded-full font-semibold">
                    <i class="fas fa-clock mr-2"></i>
                    <span id="quizTimer"><?php echo gmdate("i:s", $timer_duration); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
<div class="w-fit bg-white rounded-lg shadow-md p-3 h-fit">
    <h2 class="text-md font-semibold text-gray-800 mb-4">Questions</h2>
    <div class="space-y-2 flex flex-col items-center">
        <?php foreach ($slides as $index => $slide): ?>
            <?php 
            // Set button colors based on status
            $bgColor = 'bg-white'; // Default (unvisited)
            if (isset($user_responses[$slide['slide_id']])) {
                $bgColor = 'bg-green-500 text-white'; // Attempted (green)
            } elseif (isset($_SESSION['visited_slides'][$slide['slide_id']])) {
                $bgColor = 'bg-red-500 text-white'; // Visited but not attempted (red)
            }
            ?>
            <button 
                class="flex w-fit py-2 px-4 rounded-md <?php echo $bgColor; ?> border border-gray-200 hover:shadow-md transition-all duration-200 flex items-center justify-between <?php echo $index === 0 ? 'ring-2 ring-indigo-500' : ''; ?>"
                onclick="showSlide(<?php echo $index; ?>)"
                data-slide-id="<?php echo $slide['slide_id']; ?>"
            >
                <span><?php echo $index + 1; ?></span>
                <?php if (isset($user_responses[$slide['slide_id']])): ?>
                    <i class="fas fa-check-circle"></i>
                <?php endif; ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

            <!-- Main Content -->
            <div class="w-full lg:w-3/4">
                <form id="quizForm" method="POST" action="presentQuiz.php?session_code=<?php echo $session_code; ?>" class="space-y-6">
                    <?php foreach ($slides as $index => $slide): ?>
                        <div class="slide-container <?php echo $index === 0 ? 'block' : 'hidden'; ?> bg-white rounded-xl shadow-lg overflow-hidden" id="slide-<?php echo $slide['slide_id']; ?>">
                            <div class="p-8">
                                <div class="flex items-center mb-6">
                                    <span class="bg-indigo-100 text-indigo-800 text-sm font-semibold mr-2 px-2.5 py-0.5 rounded-full">Question <?php echo $index + 1; ?></span>
                                    <span class="text-gray-500 text-sm"><?php echo ucfirst($slide['question_type']); ?></span>
                                </div>
                                
                                <h3 class="text-xl font-semibold text-gray-800 mb-6"><?php echo htmlspecialchars($slide['question']); ?></h3>
                                
                                <?php if ($slide['question_type'] === 'multiple'): ?>
                                    <div class="space-y-4">
                                        <?php 
                                        // Fetch options for this slide
                                        $options_query = "SELECT * FROM slide_options WHERE slide_id = ? ORDER BY position";
                                        $stmt = $conn->prepare($options_query);
                                        $stmt->bind_param("i", $slide['slide_id']);
                                        $stmt->execute();
                                        $options_result = $stmt->get_result();
                                        $options = $options_result->fetch_all(MYSQLI_ASSOC);
                                        
                                        foreach ($options as $option): ?>
                                            <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 cursor-pointer transition-colors duration-200 <?php echo (isset($user_responses[$slide['slide_id']]) && $user_responses[$slide['slide_id']] == $option['option_id']) ? 'bg-indigo-50 border-indigo-300' : ''; ?>">
                                                <input 
                                                    type="radio" 
                                                    name="answers[<?php echo $slide['slide_id']; ?>]" 
                                                    value="<?php echo $option['option_id']; ?>" 
                                                    class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                                    <?php echo (isset($user_responses[$slide['slide_id']]) && $user_responses[$slide['slide_id']] == $option['option_id']) ? 'checked' : ''; ?>
                                                >
                                                <span class="ml-3 text-gray-700"><?php echo htmlspecialchars($option['option_text']); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif ($slide['question_type'] === 'fillblank'): ?>
                                    <div class="mt-4">
                                        <input 
                                            type="text" 
                                            name="answers[<?php echo $slide['slide_id']; ?>]" 
                                            placeholder="Type your answer here..." 
                                            value="<?php echo isset($user_responses[$slide['slide_id']]) ? htmlspecialchars($user_responses[$slide['slide_id']]) : ''; ?>"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                        >
                                    </div>
                                <?php elseif ($slide['question_type'] === 'shortanswer'): ?>
                                    <div class="mt-4">
                                        <textarea 
                                            name="answers[<?php echo $slide['slide_id']; ?>]" 
                                            placeholder="Type your answer here..."
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 min-h-[120px]"
                                        ><?php echo isset($user_responses[$slide['slide_id']]) ? htmlspecialchars($user_responses[$slide['slide_id']]) : ''; ?></textarea>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                                <div class="flex justify-between">
                                    <?php if ($index > 0): ?>
                                        <button 
                                            type="button" 
                                            onclick="showSlide(<?php echo $index - 1; ?>)" 
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            <i class="fas fa-arrow-left mr-2"></i> Previous
                                        </button>
                                    <?php else: ?>
                                        <div></div>
                                    <?php endif; ?>
                                    
                                    <?php if ($index < count($slides) - 1): ?>
                                        <button 
                                            type="button" 
                                            onclick="showSlide(<?php echo $index + 1; ?>)" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Next <i class="fas fa-arrow-right ml-2"></i>
                                        </button>
                                    <?php else: ?>
                                        <button 
                                            type="submit" 
                                            id="submitBtn"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        >
                                            Submit Quiz <i class="fas fa-paper-plane ml-2"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 text-center">
            <div class="text-green-500 text-5xl mb-4">
                <i class="fas fa-check-circle"></i>
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
        let isSubmitted = false;
        
        <?php if ($timer_duration > 0): ?>
        // Initialize timer only if duration is set
        let timeLeft = <?php echo $timer_duration; ?>;
        const timerElement = document.getElementById('quizTimer');
        let timerInterval;
        
        function startTimer() {
            timerInterval = setInterval(() => {
                timeLeft--;
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    if (!isSubmitted) {
                        isSubmitted = true;
                        document.getElementById('quizForm').submit();
                    }
                    return;
                }
                
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }
        
        // Start the timer when the page loads
        startTimer();
        <?php endif; ?>
        
        // Slide navigation
        function showSlide(index) {
            // Hide all slides
            document.querySelectorAll('.slide-container').forEach(slide => {
                slide.classList.add('hidden');
                slide.classList.remove('block');
            });
            
            // Show selected slide
            const slides = document.querySelectorAll('.slide-container');
            slides[index].classList.remove('hidden');
            slides[index].classList.add('block');
            
            // Mark as visited
            const slideId = slides[index].id.split('-')[1];
            const slideIdNum = parseInt(slideId);
            visitedSlides[slideIdNum] = true;
            sessionStorage.setItem('visitedSlides', JSON.stringify(visitedSlides));
            
            // Update question status in UI
            const questionButtons = document.querySelectorAll('[data-slide-id]');
            questionButtons.forEach(button => {
                const btnSlideId = parseInt(button.getAttribute('data-slide-id'));
                
                // Remove all current indicators
                button.classList.remove('ring-2', 'ring-indigo-500');
                
                // Add current indicator to active question
                if (btnSlideId === slideIdNum) {
                    button.classList.add('ring-2', 'ring-indigo-500');
                }
                
                // Update visited status if not answered
                if (!button.classList.contains('bg-green-500') && visitedSlides[btnSlideId]) {
                    button.classList.remove('bg-white');
                    button.classList.add('bg-red-500', 'text-white');
                }
            });
        }
        
        // Prevent form resubmission
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            if (isSubmitted) {
                e.preventDefault();
                return;
            }
            isSubmitted = true;
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerHTML = 'Submitting <i class="fas fa-spinner fa-spin ml-2"></i>';
        });
        
        // Initialize visited slides from session storage
        document.addEventListener('DOMContentLoaded', function() {
            Object.keys(visitedSlides).forEach(slideId => {
                const questionButton = document.querySelector(`[data-slide-id="${slideId}"]`);
                if (questionButton && !questionButton.classList.contains('bg-green-500')) {
                    questionButton.classList.remove('bg-white');
                    questionButton.classList.add('bg-red-500', 'text-white');
                }
            });
        });
    </script>
</body>
</html>