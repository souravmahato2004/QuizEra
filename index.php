<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>QuizEra</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="assets/icons/favicon.ico">
  <style>
    .font-outfit {
      font-family: 'Outfit', sans-serif;
    }
  </style>
</head>
<script src="assets/js/index.js" defer></script>
<body class="bg-white font-outfit text-gray-800">

  <!-- Navigation Bar -->
  <header class="shadow-sm">
  <div class="flex justify-between items-center px-6 py-1.5 bg-white relative">
    
    <!-- Logo -->
    <img src="assets/logo/QuizEra.png" alt="QuizEraLogo" class="h-10 w-auto">

    <!-- Nav Links with Dropdowns -->
    <nav class="hidden md:flex space-x-6 text-md font-Outfit font-medium relative">
      
      <!-- Dropdown Item -->
      <div class="relative dropdown">
        <button id="dropdown1-btn" class="hover:text-[#A435F0] flex items-center">
          QuizEra <span class="ml-1"><i class="ri-arrow-drop-down-line"></i></span></button>
        <div id="dropdown1" class="absolute hidden mt-2 w-40 bg-white border border-[#A435F0] shadow rounded z-50">
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">How to use</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">About Us</a>
        </div>
      </div>

      <div class="relative dropdown">
        <button id="dropdown2-btn" class="hover:text-[#A435F0] flex items-center">
          Features <span class="ml-1"><i class="ri-arrow-drop-down-line"></i></span>
        </button>
        <div id="dropdown2" class="absolute hidden mt-2 w-40 border border-[#A435F0] bg-white shadow rounded z-50">
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Videos</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Audio</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Polls</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Q&A</a>
        </div>
      </div>

      <div class="relative dropdown">
        <button id="dropdown3-btn" class="hover:text-[#A435F0] flex items-center">
          Contact Us <span class="ml-1"><i class="ri-arrow-drop-down-line"></i></span>
        </button>
        <div id="dropdown3" class="absolute hidden mt-2 w-40 border border-[#A435F0] bg-white shadow rounded z-50">
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">E-mail</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Call</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Facebook</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Instagram</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">X</a>
        </div>
      </div>

      <div class="relative dropdown">
      <button id="dropdown4-btn" class="hover:text-[#A435F0] flex items-center">
          Pricing <span class="ml-1"><i class="ri-arrow-drop-down-line"></i></span>
        </button>
        <div id="dropdown4" class="absolute hidden mt-2 w-40 border border-[#A435F0] bg-white shadow rounded z-50">
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Free</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Professional</a>
          <a href="#" class="block rounded px-4 py-2 hover:bg-purple-100">Community</a>
        </div>
      </div>
    </nav>

      <!-- Auth Buttons -->
      <div class="space-x-2 text-md font-Outfit font-medium">
        <a href="#" class="hover:text-[#A435F0]">Login</a>
        <a href="#" class="bg-[#A435F0] text-white px-4 py-2 rounded-full">Sign up</a>
      </div>
    </div>

    <!-- Quiz Code Join Bar -->
    <div class="relative bg-[#E1B6FF] text-center py-3 px-4 flex justify-center items-center space-x-2 text-md font-Outfit">
    <span>Enter Code to join a live quiz</span>
    <input type="text" maxlength="6" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
           placeholder="123 456" pattern="\d{6}" class="px-3 py-1 rounded border border-gray-300 w-24 text-center">
    <button class="bg-[#DFDFDF] px-4 py-1 rounded-full hover:bg-[#862BC3] hover:text-white">Join</button>
    <!-- Close Button -->
    <button onclick="this.parentElement.style.display='none'" class="absolute right-6 text-xl text-[#A435F0]">
    <i class="ri-close-circle-line"></i>
     <!-- Close Icon -->
    </button>
</div>
  </header>

  <!-- Hero Section -->
  <section class="text-center pt-16 px-4">
    <h1 class="text-3xl md:text-6xl font-Outfit font-medium mb-4">Host Live Quizzes for Free<br> on QuizEra</h1>
    <p class="text-black text-xl max-w-xl mx-auto mb-6 font-Outfit font-small">
      Engage your audience with QuizEra – host thrilling live quizzes and interact with participants in real time!
    </p>
    <a href="#" class="text-xl inline-block bg-[#E1B6FF] text-black px-6 py-2 rounded-full hover:bg-[#A435F0] hover:text-white transition">
      Get started for Free
    </a>
    <p class="text-xs text-gray-500 mt-2">No credit card needed</p>
  </section>

  <!-- Device Preview Section -->
  <video autoplay muted loop playsinline style="width: 100%; height: auto;"><source src="assets/video/QuizEraAnimation.mp4" type="video/mp4">Your browser does not support the video tag.</video>
  <section class="py-16 px-4 bg-white text-center">
  <h2 class="text-2xl sm:text-4xl font-semibold text-[#A435F0] mb-2">
    Engaging Online Quizzes for Every Occasion!
  </h2>
  <p class="pb-20 max-w-xl mx-auto text-[#1E1E1E] mb-10">
    With thousands of ready-made quizzes on demand, diverse question types, and national leaderboards, QuizEra is your ultimate destination for fun and competitive quizzing. Perfect for:
  </p>

  <div class="max-w-6xl mx-auto space-y-12">
    
    <!-- Family & Friends -->
    <div class="flex flex-col pb-12 md:flex-row items-center md:items-start gap-6">
      <img src="assets/images/FamilyPic.png" alt="Family and Friends" class="w-full md:w-1/2 rounded-3xl shadow-md">
      <div class="text-left md:w-1/2 flex flex-col justify-center text-center">
        <h3 class="text-4xl mt-10 font-semibold text-[#FE4D48] mb-2">Family & Friends</h3>
        <p class="text-black mb-4 py-2">
          Gather your friends and family for a fun, interactive quiz experience! With categories ranging from trending topics to nostalgia, QuizEra makes quiz nights unforgettable. Let the fun begin!
        </p>
        <div><button class="bg-gray-200 text-gray-800 px-4 py-2 w-fit rounded-full hover:bg-[#FFA83D]">Learn More</button></div>
      </div>
    </div>

    <!-- Schools & Colleges -->
    <div class="flex flex-col pb-12 md:flex-row-reverse items-center md:items-start gap-6">
      <img src="assets/images/image.png" alt="Schools and Colleges" class="w-full md:w-1/2 rounded-3xl shadow-md">
      <div class="text-left md:w-1/2 flex flex-col justify-center text-center">
        <h3 class="text-4xl mt-10 font-semibold text-[#FEC02C] mb-2">Schools & Colleges</h3>
        <p class="text-black mb-4 py-2">
          Bring your classrooms and peers together for an exciting, quiz-based learning experience. QuizEra's quizzes are perfect for skill assessment, engagement, and group competitions.
        </p>
        <div><button class="bg-gray-200 text-gray-800 px-4 py-2 w-fit rounded-full hover:bg-[#FFA83D]">Learn More</button></div>
      </div>
    </div>

    <!-- Events & Occasions -->
    <div class="flex flex-col pb-12 md:flex-row items-center md:items-start gap-6">
      <img src="assets/images/image2.png" alt="Events and Occasions" class="w-full md:w-1/2 rounded-3xl shadow-md">
      <div class="text-left md:w-1/2 flex flex-col justify-center text-center">
        <h3 class="text-4xl mt-10 font-semibold text-[#4A1F96] mb-2">Events & Occasions</h3>
        <p class="text-black mb-4 py-2">
          Make every event and occasion unforgettable with a QuizEra quiz! From birthdays to office parties, our quizzes add energy, laughter, and a touch of friendly rivalry.
        </p>
        <div><button class="bg-gray-200 text-gray-800 px-4 py-2 w-fit rounded-full hover:bg-[#FFA83D]">Learn More</button></div>
      </div>
    </div>

    <!-- Survey & Feedback -->
    <div class="flex flex-col pb-12 md:flex-row-reverse items-center md:items-start gap-6">
      <img src="assets/images/image3.png" alt="Survey and Feedback" class="w-full md:w-1/2 rounded-3xl shadow-md">
      <div class="text-left md:w-1/2 flex flex-col justify-center text-center">
        <h3 class="text-4xl mt-10 font-semibold text-[#78D6FE] mb-2">Survey & Feedback</h3>
        <p class="text-black mb-4 py-2">
          Turn surveys and feedback into an engaging experience with QuizEra! Whether for product reviews or training feedback, make responses easy, insightful, and enjoyable.
        </p>
        <div><button class="bg-gray-200 text-gray-800 px-4 py-2 w-fit rounded-full hover:bg-[#FFA83D]">Learn More</button></div>
      </div>
    </div>

  </div>
</section>
<section class="py-16 px-6 text-center bg-white">
  <!-- Headings -->
  <h2 class="text-2xl md:text-4xl font-semibold text-[#525252] mb-2">
    Create, host, and play exciting Quizzes in just Minutes<br>Fast, Fun, and Effortless!
  </h2>
  <h3 class="text-4xl pt-4 font-medium text-[#A435F0] mb-10">Get started in 3 steps</h3>

  <!-- 3 Steps Grid -->
  <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
    
    <!-- Create -->
    <div class="border-2 border-[#E1B6FF] rounded-xl p-6 shadow-sm hover:shadow-md hover:border-[#A435F0] transition">
      <div><h4 class="text-4xl font-medium text-[#FE4D48] flex mb-4">Create</h4></div>
      <img src="assets/images/create.png" alt="Create Quiz" class="w-full h-auto rounded-xl mx-auto mb-4 object-contain">
      <p class="text-black text-sm font-medium">
        Choose from a predefined templates for a quick and hassle-free setup, or Create your own quiz by selecting questions
      </p>
    </div>

    <!-- Host -->
    <div class="border-2 border-[#E1B6FF] rounded-xl p-6 shadow-sm hover:shadow-md hover:border-[#A435F0] transition">
    <div><h4 class="text-4xl font-medium text-[#165DFC] flex mb-4">Host</h4></div>
      <img src="assets/images/host.png" alt="Host Quiz" class="w-full h-auto rounded-xl mx-auto mb-4 object-contain">
      <p class="text-black text-sm font-medium">
        Once you’ve selected your questions, hosting the quiz is a breeze! With just a Clicks, launch your quiz
      </p>
    </div>

    <!-- Play -->
    <div class="border-2 border-[#E1B6FF] rounded-xl p-6 shadow-sm hover:shadow-md hover:border-[#A435F0] transition">
    <div><h4 class="text-4xl font-medium text-[#39D766] flex mb-4">Play</h4></div>
      <img src="assets/images/play.png" alt="Play Quiz" class="w-full h-auto rounded-xl mx-auto mb-4 object-contain">
      <p class="text-black text-sm font-medium">
        No any extra step, Now just hit play and enjoy the game!<br>Have fun
      </p>
    </div>

  </div>

  <p class="text-xl text-black mt-10">See! Taking Quiz in QuizEra is as Simple as that</p>
</section>
<section class="bg-[url('assets/images/design1.png')] bg-cover bg-center text-white text-center py-16 px-4 z-50 w-auto h-[296px]">
<!-- <section class="bg-purple-300 text-white text-center py-16 px-4"> -->
  <h2 class="text-2xl md:text-4xl font-medium mb-6">
    Create your Free account and get quizzing today!
  </h2>
  <button class="bg-[#525252] text-white font-medium px-9 py-4 rounded-full transition">
    Get started now
  </button>
</section>
<footer class="bg-white text-gray-800 pt-10 w-full">
<div class="w-full mx-auto py-10 px-4 flex flex-col md:flex-row justify-between items-start space-y-10 md:space-y-0 md:space-x-12">

  <!-- Logo Section -->
  <div class="flex flex-col items-center ml-10 pl-8 pr-[200px] md:items-start space-y-4 md:min-w-[150px]">
    <img src="assets/logo/QuizEraLogo.png" alt="QuizEra Logo" class="w-[300px] h-auto">
    <img src="assets/logo/QuizEra.png" alt="QuizEra Text Logo" class="w-[300px] h-auto">
  </div>

  <!-- Links Section -->
  <div class="flex flex-wrap flex-grow gap-28 text-gray-800">
    
    <!-- Features -->
    <div>
      <h4 class="font-semibold mb-2 border-b border-gray-400 w-fit">Features</h4>
      <ul class="space-y-1 text-sm">
        <li><a href="#" class="hover:underline">Overview</a></li>
        <li><a href="#" class="hover:underline">Quiz</a></li>
        <li><a href="#" class="hover:underline">Cloud Storage</a></li>
        <li><a href="#" class="hover:underline">Q&A</a></li>
        <li><a href="#" class="hover:underline">Survey</a></li>
        <li><a href="#" class="hover:underline">Feedback</a></li>
        <li><a href="#" class="hover:underline">Live polling</a></li>
        <li><a href="#" class="hover:underline">Leaderboard</a></li>
      </ul>
    </div>

    <!-- Resources -->
    <div>
      <h4 class="font-semibold mb-2 border-b border-gray-400 w-fit">Resources</h4>
      <ul class="space-y-1 text-sm">
        <li><a href="#" class="hover:underline">How to Use</a></li>
        <li><a href="#" class="hover:underline">Templates</a></li>
        <li><a href="#" class="hover:underline">Figma Design</a></li>
        <li><a href="#" class="hover:underline">Pricing</a></li>
      </ul>
    </div>

    <!-- Details -->
    <div>
      <h4 class="font-semibold mb-2 border-b border-gray-400 w-fit">Details</h4>
      <ul class="space-y-1 text-sm">
        <li><a href="#" class="hover:underline">Legal</a></li>
        <li><a href="#" class="hover:underline">Policies</a></li>
        <li><a href="#" class="hover:underline">Accessibility</a></li>
        <li><a href="#" class="hover:underline">Help Center</a></li>
        <li><a href="#" class="hover:underline">Requirements</a></li>
      </ul>
    </div>

    <!-- About Us -->
    <div>
      <h4 class="font-semibold mb-2 border-b border-gray-400 w-fit">About Us</h4>
      <ul class="space-y-1 text-sm">
        <li><a href="#" class="hover:underline">The Team</a></li>
        <li><a href="#" class="hover:underline">Roles</a></li>
        <li><a href="#" class="hover:underline">Contact Us</a></li>
        <li><a href="#" class="hover:underline">Feedback</a></li>
        <li><a href="#" class="hover:underline">Need Help</a></li>
        <li><a href="#" class="hover:underline">E-Mail</a></li>
        <li><a href="#" class="hover:underline">Address</a></li>
      </ul>
    </div>

  </div>
  </div>


    <!-- Bottom Bar -->
    <div class=" border-t-2 border-[#A435F0] mt-10 py-4 px-10 flex flex-col md:flex-row justify-between items-center text-md">
    <span>© Copyright <span id="year"></span>–QuizEra, All rights Reserved</span>
      <div class="flex items-center space-x-4 mt-2 text-[#424644] md:mt-0">
        <span>Follow our Social media handles</span>
        <!-- Replace # with real links -->
        <a href="#" class="text-2xl"><i class="ri-figma-line" alt="Figma" class="w-auto h-8"></i></a>
        <a href="#" class="text-2xl"><i class="ri-instagram-fill" alt="Instagram" class="w-5 h-5"></i></a>
        <a href="#" class="text-2xl"><i class="ri-facebook-circle-fill" alt="Facebook" class="w-5 h-5"></i></a>
        <a href="#" class="text-2xl"><i class="ri-linkedin-box-fill" alt="LinkedIn" class="w-5 h-5"></i></a>
        <a href="#" class="text-2xl"><i class="ri-twitter-x-fill" alt="Twitter" class="w-5 h-5"></i></a>
      </div>
</div>
</footer>
</body>
<!-- script for copright year -->
<script>
  document.getElementById("year").textContent = new Date().getFullYear();
</script>
</html>