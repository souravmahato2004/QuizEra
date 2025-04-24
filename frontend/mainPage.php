<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>QuizEra Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
    <style>
    .font-outfit {
      font-family: 'Outfit', sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100 h-screen w-screen overflow-hidden flex font-outfit">
    <div class="w-[363px] bg-white shadow-md h-full fixed flex flex-col px-4 border-r border-[#BE92DC]">
        <div class="flex flex-row items-center h-1/6 w-full">
            <img src="../assets/logo/QuizEra.png" alt="Logo" class="w-2/3 h-1/2">
            <div class="w-fit h-7 ml-10 bg-purple-200 rounded-2xl border-2 border-purple-300 text-purple-600 px-1">FreePlan</div>
        </div>

        <button class="bg-[#9D3AE3] text-white text-3xl shadow-[0_4px_6px_-1px_rgba(107,33,168,0.8)] rounded-md py-2 mb-6 active:scale-95 transition duration-200 ease-in-out">+ Create</button>

        <ul class="space-y-3 text-black text-sm pl-5">
            <li class="flex flex-row">
                <img src="../assets/icons/quizera.png" alt="Icon" class="w-7 h-7 pt-1 pr-1">
                <a href="#" class="flex justify-center items-center text-xl text-[#9D3AE3] font-medium -ml-1 hover:text-2xl transition-all duration-100">QuizEra</a>
            </li>
            <li>
                <i class="ri-home-line text-xl"></i>
                <a href="#" class="text-xl text-[#9D3AE3] font-medium hover:text-2xl transition-all duration-300"> Home</a>
            </li>
            <li>
                <i class="ri-stack-line text-xl"></i>
                <a href="#" class="text-xl text-[#9D3AE3] font-medium hover:text-2xl transition-all duration-100"> My Quizzes</a>
            </li>
            <li>
                <i class="ri-booklet-line text-xl"></i>
                <a href="#" class="text-xl text-[#9D3AE3] font-medium hover:text-2xl transition-all duration-100"> Browse Templates</a>
            </li>
            <li>
                <i class="ri-share-circle-line text-xl"></i>
                <a href="#" class="text-xl text-[#9D3AE3] font-medium hover:text-2xl transition-all duration-100"> Shared with me</a>
            </li>
        </ul>

        <div class="mt-24 pl-5">
            <i class="ri-wallet-3-fill text-lg text-[#797979]"></i>
            <a href="#" class="text-[#797979] text-xl text-sm font-medium hover:underline mb-4"> Upgrade Your Existing Plan</a>
        </div>

        <div class="mt-auto text-md space-y-2 text-[#797979] font-small pb-2 pl-5">
            <a href="#" class="block">Help & Support</a>
            <a href="#" class="block">Contact Us</a>
            <a href="#" class="block">Terms & Conditions</a>
        </div>

    </div>

    <div class="bg-white w-[1077px] ml-[363px] flex-1 overflow-y-auto">
        
        <?php include '../frontend/header.php'?>

        <input type="checkbox" id="close-banner" class="hidden peer" />

        <div class="peer-checked:hidden relative flex items-center justify-center h-10 w-full bg-[#999999] text-white px-4">
            <p class="text-center text-sm sm:text-base">
                You're just one step away from more power - Upgrade Now!
            </p>

            <label for="close-banner" class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-white text-xl leading-none hover:text-black">
                &times;
            </label>
        </div>


        <div class="flex flex-col justify-center items-center bg-[#E0B3FF] px-6 mb-4 py-14">
            <h1 class="text-5xl font-medium text-white pb-4">Welcome, QuizEra UserName</h1>
            <div class="relative mt-4 w-2/5">
                <input
                    type="text"
                    placeholder="Search Pages, Templates and Topics"
                    class="w-full p-2 pl-10 rounded-3xl bg-[#D9D9D9] text-[#797979]">
                <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-[#797979]"></i>
            </div>
        </div>


        <div class="mb-4 flex space-x-4 pl-5">
            <button class="bg-red-500 text-white px-7 py-2 rounded-3xl hover:bg-red-600 tranisition duration-500">Build New Quiz</button>
            <button class="bg-yellow-400 text-white px-7 py-2 rounded-3xl hover:bg-yellow-500 tranisition duration-500">Browse Templete</button>
            <button class="bg-blue-300 text-white px-7 py-2 rounded-3xl hover:bg-blue-500 tranisition duration-500">Open Saved Quiz</button>
        </div>

        <div class="px-4 py-16">
            <h2 class="text-lg font-small mb-2">Recently Viewed</h2>
            <p class="flex items-center justify-center font-small text-xl text-black">Looks like you haven't worked on anything yet. Time to create a one Now!</p>
        </div>

        <div class="mt-6 p-4">
            <div class="flex flex-row">
                <h2 class="text-lg font-small mb-2 w-4/5">Popular Templates</h2>
                <p class="flex items-center justify-center text-lg font-small py-[2px] mb-2 bg-[#BDBDBD] rounded-xl w-1/6 hover:bg-gray-400 transition duration-500 hover:cursor-pointer">See all Templates <i class="ri-arrow-right-s-line"></i></p>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-[#F8EEFF] p-4 rounded-2xl hover:border hover:border-[#9D3AE3] hover:cursor-pointer">
                    <img src="../assets/images/image1.png" alt="image1" class="rounded object-contain w-full">
                    <p class="mt-2">Multiple Choice Template</p>
                </div>
                <div class="bg-[#F8EEFF] p-4 rounded-2xl hover:border hover:border-[#9D3AE3] hover:cursor-pointer">
                    <img src="../assets/images/image2.png" alt="image1" class="rounded object-contain w-full">
                    <p class="mt-2">Fill in the Blank Template</p>
                </div>
                <div class="bg-[#F8EEFF] p-4 rounded-2xl hover:border hover:border-[#9D3AE3] hover:cursor-pointer">
                    <img src="../assets/images/image3.png" alt="image1" class="rounded object-contain w-full">
                    <p class="mt-2">Survey Quiz Template</p>
                </div>
            </div>
        </div>
        <div class="mt-6 p-4">
            <h2 class="text-lg font-small mb-2">Getting Started With QuizEra</h2>
            <div class="grid grid-cols-4 gap-3">
                <div class="bg-[#D9D9D9] p-4 border-2 border-gray-400 rounded-2xl hover:scale-110 transition duration-400">
                    <img src="../assets/icons/glasses-line.svg" alt="glass" class="h-4 w-4" />
                    <p class="mt-2">What is QuizEra</p>
                    <p class="text-gray-600">2 Min Read</p>
                </div>
                <div class="bg-[#D9D9D9] p-4 border-2 border-gray-400 rounded-2xl hover:scale-110 transition duration-400">
                    <i class="ri-bar-chart-2-line"></i>
                    <p class="mt-2">How to create your first Quiz</p>
                    <p class="text-gray-600">2 Min Video</p>
                </div>
                <div class="bg-[#D9D9D9] p-4 border-2 border-gray-400 rounded-2xl hover:scale-110 transition duration-400">
                    <i class="ri-upload-cloud-2-line"></i>
                    <p class="mt-2">How to present</p>
                    <p class="text-gray-600">2 Min Video</p>
                </div>
                <div class="bg-[#D9D9D9] p-4 border-2 border-gray-400 rounded-2xl hover:scale-110 transition duration-400">
                    <i class="ri-team-line"></i>
                    <p class="mt-2">How participants join</p>
                    <p class="text-gray-600">2 Min Video</p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>