<!DOCTYPE html>
<html lang="en">
<?php include '../backend/mainpageBackend.php';?>

<head>
    <meta charset="UTF-8">
    <title>QuizEra Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
    <style>
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100 h-screen w-screen overflow-hidden flex font-outfit">
    <div id="quizModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 w-96">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Create New Quiz</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <form id="quizForm" action="../frontend/mainPage.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="newQuiz" value="createNewQuiz">
                <div class="mb-4">
                    <label for="quizName" class="block text-sm font-medium text-gray-700 mb-1">Quiz Name *</label>
                    <input type="text" id="quizName" name="quizName" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="Enter quiz name">
                </div>

                <div class="mb-4">
                    <label for="quizDescription"
                        class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="quizDescription" name="quizDescription" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="Briefly describe your quiz (optional)"></textarea>
                </div>

                <div class="mb-4">
                    <label for="quizImage" class="block text-sm font-medium text-gray-700 mb-1">Quiz Image
                        (Optional)</label>
                    <div class="mt-1 flex items-center">
                        <label for="quizImage" class="cursor-pointer">
                            <span
                                class="inline-block px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Choose File
                            </span>
                            <input id="quizImage" name="quizImage" type="file" class="sr-only" accept="image/*">
                        </label>
                        <span id="fileName" class="ml-2 text-sm text-gray-500">No file chosen</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>


    <div class="w-[363px] bg-white shadow-md h-full fixed flex flex-col px-4 border-r border-[#BE92DC]">
        <div class="flex flex-row items-center h-1/6 w-full">
            <a href="mainPage.php" class="w-2/3 h-1/2"><img src="../assets/logo/QuizEra.png" alt="Logo"
                    class="w-full h-full"></a>
            <div class="w-fit h-7 ml-10 bg-purple-200 rounded-2xl border-2 border-purple-300 text-purple-600 px-1">
                FreePlan</div>
        </div>

        <button onclick="openModal()"
            class="bg-[#9D3AE3] text-white text-3xl shadow-[0_4px_6px_-1px_rgba(107,33,168,0.8)] rounded-md py-2 mb-6 active:scale-95 transition duration-200 ease-in-out">+
            Create</button>

        <ul class="space-y-3 text-black text-sm pl-5">
            <li class="flex flex-row">
                <img src="../assets/icons/quizera.png" alt="Icon" class="w-7 h-7 pt-1 pr-1">
                <a href="#"
                    class="flex justify-center items-center text-xl text-[#9D3AE3] font-medium -ml-1 hover:text-2xl transition-all duration-100">QuizEra</a>
            </li>
            <li>
                <i class="ri-home-line text-xl"></i>
                <a href="#" class="text-xl text-[#9D3AE3] font-medium hover:text-2xl transition-all duration-300">
                    Home</a>
            </li>
            <li>
                <i class="ri-stack-line text-xl"></i>
                <a href="#" class="text-xl text-[#9D3AE3] font-medium hover:text-2xl transition-all duration-100"> My
                    Quizzes</a>
            </li>
            <li>
                <i class="ri-booklet-line text-xl"></i>
                <a href="#" class="text-xl text-[#9D3AE3] font-medium hover:text-2xl transition-all duration-100">
                    Browse Templates</a>
            </li>
            <li>
                <i class="ri-share-circle-line text-xl"></i>
                <a href="#" class="text-xl text-[#9D3AE3] font-medium hover:text-2xl transition-all duration-100">
                    Shared with me</a>
            </li>
        </ul>

        <div class="mt-24 pl-5">
            <i class="ri-wallet-3-fill text-lg text-[#797979]"></i>
            <a href="subscriptionPage.php" class="text-[#797979] text-xl text-sm font-medium hover:underline mb-4">
                Upgrade Your Existing Plan</a>
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

        <div
            class="peer-checked:hidden relative flex items-center justify-center h-10 w-full bg-[#999999] text-white px-4">
            <p class="text-center text-sm sm:text-base">
                You're just one step away from more power - Upgrade Now!
            </p>

            <label for="close-banner"
                class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-white text-xl leading-none hover:text-black">
                &times;
            </label>
        </div>


        <div class="flex flex-col justify-center items-center bg-[#E0B3FF] px-6 mb-4 py-14">
            <h1 class="text-5xl font-medium text-white pb-4">Welcome, <?php echo $_SESSION['username']; ?></h1>
            <div class="relative mt-4 w-2/5">
                <input type="text" placeholder="Search Pages, Templates and Topics"
                    class="w-full p-2 pl-10 rounded-3xl bg-[#D9D9D9] text-[#797979]">
                <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-[#797979]"></i>
            </div>
        </div>


        <div class="mb-4 flex space-x-4 pl-5">
            <button onclick="openModal()"
                class="bg-red-500 text-white px-7 py-2 rounded-3xl hover:bg-red-600 tranisition duration-500">Build New
                Quiz</button>
            <button
                class="bg-yellow-400 text-white px-7 py-2 rounded-3xl hover:bg-yellow-500 tranisition duration-500">Browse
                Templete</button>
            <button class="bg-blue-300 text-white px-7 py-2 rounded-3xl hover:bg-blue-500 tranisition duration-500">Open
                Saved Quiz</button>
        </div>

        <div class="px-4 py-8">
            <h2 class="text-lg font-small mb-2">Recently Viewed</h2>
            <?php if (empty($quizzes)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600 mb-4">You don't have any quizzes yet.</p>
                <a href="#" class="text-purple-600 hover:text-purple-800 font-medium">
                    Create your first quiz →
                </a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-3 gap-4">
                <?php foreach ($quizzes as $quiz): ?>
                    <a href="quiz.php?quiz=<?php $id=$quiz['quiz_id']; echo ($id)?>">
                    <div class="bg-[#F8EEFF] p-4 rounded-2xl hover:border hover:border-[#9D3AE3] hover:cursor-pointer">
                    <!-- Quiz Image -->
                    <?php if (!empty($quiz['image_url'])): ?>
                    <img src="../assets/quizImage/<?php echo($quiz['image_url']); ?>" alt="../assets/quizImage/default.png" class="rounded-lg object-fit w-full h-40" >
                    <?php else: ?>
                    <div
                        class="w-full h-40 bg-gradient-to-r from-purple-100 to-blue-100 rounded flex items-center justify-center">
                        <span class="text-gray-500">No image</span>
                    </div>
                    <?php endif; ?>

                    <!-- Quiz Info -->
                    <div class="mt-2">
                        <h3 class="font-semibold text-lg truncate"><?= htmlspecialchars($quiz['title']) ?></h3>
                        <p class="text-gray-600 text-sm mt-1 line-clamp-2">
                            <?= htmlspecialchars($quiz['description'] ?? 'No description') ?>
                        </p>
                        <div class="flex justify-between items-center mt-2 text-xs">
                            <span class="text-gray-500">
                                <?= date('M j, Y', strtotime($quiz['updated_at'])) ?>
                            </span>
                            <span class="<?= $quiz['is_published'] ? 'text-green-600' : 'text-yellow-600' ?>">
                                <?= $quiz['is_published'] ? 'Published' : 'Draft' ?>
                            </span>
                        </div>
                    </div>
                </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="mt-6 p-4">
            <div class="flex flex-row">
                <h2 class="text-lg font-small mb-2 w-4/5">Popular Templates</h2>
                <p
                    class="flex items-center justify-center text-lg font-small py-[2px] mb-2 bg-[#BDBDBD] rounded-xl w-1/6 hover:bg-gray-400 transition duration-500 hover:cursor-pointer">
                    See all Templates <i class="ri-arrow-right-s-line"></i></p>
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
                <div
                    class="bg-[#D9D9D9] p-4 border-2 border-gray-400 rounded-2xl hover:scale-110 transition duration-400">
                    <img src="../assets/icons/glasses-line.svg" alt="glass" class="h-4 w-4" />
                    <p class="mt-2">What is QuizEra</p>
                    <p class="text-gray-600">2 Min Read</p>
                </div>
                <div
                    class="bg-[#D9D9D9] p-4 border-2 border-gray-400 rounded-2xl hover:scale-110 transition duration-400">
                    <i class="ri-bar-chart-2-line"></i>
                    <p class="mt-2">How to create your first Quiz</p>
                    <p class="text-gray-600">2 Min Video</p>
                </div>
                <div
                    class="bg-[#D9D9D9] p-4 border-2 border-gray-400 rounded-2xl hover:scale-110 transition duration-400">
                    <i class="ri-upload-cloud-2-line"></i>
                    <p class="mt-2">How to present</p>
                    <p class="text-gray-600">2 Min Video</p>
                </div>
                <div
                    class="bg-[#D9D9D9] p-4 border-2 border-gray-400 rounded-2xl hover:scale-110 transition duration-400">
                    <i class="ri-team-line"></i>
                    <p class="mt-2">How participants join</p>
                    <p class="text-gray-600">2 Min Video</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function openModal() {
        document.getElementById('quizModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('quizModal').classList.add('hidden');
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('quizModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    // File name display
    document.getElementById('quizImage').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        document.getElementById('fileName').textContent = fileName;
    });
    </script>
</body>

</html>