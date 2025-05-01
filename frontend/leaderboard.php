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
        .top-three {
            height: 240px;
        }
        .first-place {
            height: 220px;
            transform: translateY(-20px);
        }
        .second-place, .third-place {
            height: 200px;
        }
        #questionModal {
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- New Header Section -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <button id="backButton" class="flex items-center text-purple-600 hover:text-purple-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Back to Home</span>
            </button>
            <h1 class="text-xl font-bold text-gray-800">Quiz Results</h1>
            <div class="w-8"></div> <!-- Spacer for balance -->
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
    
    <script src="../assets/js/leaderboard.js"></script>
</body>
</html>