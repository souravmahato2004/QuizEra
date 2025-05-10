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
    <!-- Create New Quiz Modal -->
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

    <!-- Edit Quiz Modal -->
    <div id="editQuizModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 w-96">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Edit Quiz</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <form id="editQuizForm" action="../backend/updateQuiz.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editQuizId" name="quizId">
                <div class="mb-4">
                    <label for="editQuizName" class="block text-sm font-medium text-gray-700 mb-1">Quiz Name *</label>
                    <input type="text" id="editQuizName" name="quizName" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="Enter quiz name">
                </div>

                <div class="mb-4">
                    <label for="editQuizDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="editQuizDescription" name="quizDescription" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="Briefly describe your quiz (optional)"></textarea>
                </div>

                <div class="mb-4">
                    <label for="editQuizImage" class="block text-sm font-medium text-gray-700 mb-1">Quiz Image</label>
                    <div class="mt-1 flex items-center">
                        <label for="editQuizImage" class="cursor-pointer">
                            <span
                                class="inline-block px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Change Image
                            </span>
                            <input id="editQuizImage" name="quizImage" type="file" class="sr-only" accept="image/*">
                        </label>
                        <span id="editFileName" class="ml-2 text-sm text-gray-500">Keep current</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                    <div id="currentImagePreview" class="mt-2"></div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 w-96">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Confirm Delete</h3>
                <button onclick="closeDeleteModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <p class="mb-6">Are you sure you want to delete this quiz? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Delete
                </button>
            </div>
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
        <a href="../frontend/leaderboard.php?session_id=22&quiz_id=28&user_id=<?php echo($_SESSION['id'])?>">leaderboard</a>
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
                <button onclick="openModal()" class="text-purple-600 hover:text-purple-800 font-medium">
                    Create your first quiz →
            </button>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-3 gap-4">
                <?php foreach ($quizzes as $quiz): ?>
                <div class="bg-[#F8EEFF] p-4 rounded-2xl hover:border hover:border-[#9D3AE3] hover:cursor-pointer relative group">
                    <!-- Three-dot menu button -->
                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <button onclick="event.stopPropagation(); toggleMenu('menu-<?= $quiz['quiz_id'] ?>')" 
                                class="p-1 rounded-full hover:bg-gray-200">
                            <i class="ri-more-2-fill"></i>
                        </button>
                        <!-- Dropdown menu -->
                        <div id="menu-<?= $quiz['quiz_id'] ?>" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg z-10">
                            <a href="quiz.php?quiz=<?= $quiz['quiz_id'] ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Open</a>
                            <button onclick="event.stopPropagation(); openEditModal(<?= $quiz['quiz_id'] ?>, '<?= htmlspecialchars($quiz['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($quiz['description'] ?? '', ENT_QUOTES) ?>', '<?= $quiz['image_url'] ?? '' ?>')"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</button>
                            <button onclick="event.stopPropagation(); confirmDelete(<?= $quiz['quiz_id'] ?>)"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</button>
                        </div>
                    </div>
                    
                    <!-- Quiz Image -->
                    <?php if (!empty($quiz['image_url'])): ?>
                    <img src="../assets/quizImage/<?= htmlspecialchars($quiz['image_url']) ?>" alt="Quiz Image" 
                         class="rounded-lg object-fit w-full h-40" onclick="window.location.href='quiz.php?quiz=<?= $quiz['quiz_id'] ?>'">
                    <?php else: ?>
                    <div class="w-full h-40 bg-gradient-to-r from-purple-100 to-blue-100 rounded flex items-center justify-center"
                         onclick="window.location.href='quiz.php?quiz=<?= $quiz['quiz_id'] ?>'">
                        <span class="text-gray-500">No image</span>
                    </div>
                    <?php endif; ?>

                    <!-- Quiz Info -->
                    <div class="mt-2" onclick="window.location.href='quiz.php?quiz=<?= $quiz['quiz_id'] ?>'">
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
    // Toggle dropdown menu
    function toggleMenu(menuId) {
        const menu = document.getElementById(menuId);
        menu.classList.toggle('hidden');
    }

    // Close all dropdown menus when clicking elsewhere
    document.addEventListener('click', function(event) {
        if (!event.target.matches('.ri-more-2-fill') && !event.target.closest('.relative.group button')) {
            const menus = document.querySelectorAll('[id^="menu-"]');
            menus.forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });

    // Create Quiz Modal Functions
    function openModal() {
        document.getElementById('quizModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('quizModal').classList.add('hidden');
    }

    // Edit Quiz Modal Functions
    function openEditModal(quizId, title, description, imageUrl) {
        document.getElementById('editQuizId').value = quizId;
        document.getElementById('editQuizName').value = title;
        document.getElementById('editQuizDescription').value = description || '';
        
        const previewDiv = document.getElementById('currentImagePreview');
        previewDiv.innerHTML = '';
        
        if (imageUrl) {
            const img = document.createElement('img');
            img.src = `../assets/quizImage/${imageUrl}`;
            img.alt = 'Current Quiz Image';
            img.className = 'w-full h-32 object-contain rounded';
            previewDiv.appendChild(img);
        } else {
            previewDiv.innerHTML = '<p class="text-gray-500">No image currently set</p>';
        }
        
        document.getElementById('editQuizModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editQuizModal').classList.add('hidden');
    }

    // Delete Confirmation Modal Functions
    let quizToDelete = null;

    function confirmDelete(quizId) {
        quizToDelete = quizId;
        document.getElementById('deleteConfirmModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        quizToDelete = null;
        document.getElementById('deleteConfirmModal').classList.add('hidden');
    }

    // Handle delete confirmation
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (quizToDelete) {
            fetch('../backend/deleteQuiz.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `quizId=${quizToDelete}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error deleting quiz: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        closeDeleteModal();
    });

    // File name display for create modal
    document.getElementById('quizImage').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        document.getElementById('fileName').textContent = fileName;
    });

    // File name display for edit modal
    document.getElementById('editQuizImage').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Keep current';
        document.getElementById('editFileName').textContent = fileName;
    });

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('quizModal');
        if (event.target === modal) {
            closeModal();
        }
        
        const editModal = document.getElementById('editQuizModal');
        if (event.target === editModal) {
            closeEditModal();
        }
        
        const deleteModal = document.getElementById('deleteConfirmModal');
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    }
    </script>
</body>
</html>