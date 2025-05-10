<!DOCTYPE html>
<html lang="en">
<?php include '../backend/quizBackend.php';?>
<head>
    <meta charset="UTF-8">
    <title>Quiz Question</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
    <style>
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }

    .slidebar {
        height: calc(100vh - 180px);
        overflow-y: auto;
    }

    .slidebar::-webkit-scrollbar {
        width: 6px;
    }

    .slidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .slidebar::-webkit-scrollbar-thumb {
        background: #A435F0;
        border-radius: 3px;
    }

    .slidebar::-webkit-scrollbar-thumb:hover {
        background: #8a2be2;
    }

    [contenteditable="true"]:focus {
        outline: none;
        border-bottom: 1px dashed #A435F0;
    }
    </style>
</head>

<body class="bg-[#E5E5E5] min-h-screen flex flex-col font-outfit">
    <div class="relative">
        <input type="checkbox" id="close-banner" class="hidden peer" />
        <div class="bg-[#E1B6FF] py-2 flex flex-row items-center pl-4 relative peer-checked:hidden h-12">
            <p class="text-lg font-small">Unlock unlimited participants and new features.</p>
            <button
                class="flex flex-row h-fit bg-[#EFDAFE] text-[#A435F0] border-2 border-[#A435F0] rounded-xl px-2 ml-2 hover:bg-white transition duration-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px] text-[#A435F0] py-[2px]"
                    viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12.0049 22.0027C6.48204 22.0027 2.00488 17.5256 2.00488 12.0027C2.00488 6.4799 6.48204 2.00275 12.0049 2.00275C17.5277 2.00275 22.0049 6.4799 22.0049 12.0027C22.0049 17.5256 17.5277 22.0027 12.0049 22.0027ZM12.0049 20.0027C16.4232 20.0027 20.0049 16.421 20.0049 12.0027C20.0049 7.58447 16.4232 4.00275 12.0049 4.00275C7.5866 4.00275 4.00488 7.58447 4.00488 12.0027C4.00488 16.421 7.5866 20.0027 12.0049 20.0027ZM8.50488 14.0027H14.0049C14.281 14.0027 14.5049 13.7789 14.5049 13.5027C14.5049 13.2266 14.281 13.0027 14.0049 13.0027H10.0049C8.62417 13.0027 7.50488 11.8835 7.50488 10.5027C7.50488 9.12203 8.62417 8.00275 10.0049 8.00275H11.0049V6.00275H13.0049V8.00275H15.5049V10.0027H10.0049C9.72874 10.0027 9.50488 10.2266 9.50488 10.5027C9.50488 10.7789 9.72874 11.0027 10.0049 11.0027H14.0049C15.3856 11.0027 16.5049 12.122 16.5049 13.5027C16.5049 14.8835 15.3856 16.0027 14.0049 16.0027H13.0049V18.0027H11.0049V16.0027H8.50488V14.0027Z">
                    </path>
                </svg>Upgrade?</button>
            <label for="close-banner"
                class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-[#A435F0] text-xl leading-none hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px] text-[#A435F0] hover:text-purple-700"
                    viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM12 10.5858L14.8284 7.75736L16.2426 9.17157L13.4142 12L16.2426 14.8284L14.8284 16.2426L12 13.4142L9.17157 16.2426L7.75736 14.8284L10.5858 12L7.75736 9.17157L9.17157 7.75736L12 10.5858Z">
                    </path>
                </svg>
            </label>
        </div>
    </div>

    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
        <div class="flex items-center gap-2">
            <!-- Back Button -->
            <button onclick="window.location.href='mainPage.php'" class=" top-10 left-10 text-black z-20">
                <i class="ri-arrow-left-long-line"></i>
            </button>
            <span class="text-lg font-small"> <?php echo htmlspecialchars($title);?></span>
        </div>

        <div class="flex gap-4">
            <a href="../frontend/quiz.php?quiz=<?php echo($_GET['quiz']);?>" class="underline text-lg">Create</a>
            <a href="../frontend/leaderboard.php?session_id=<?php echo($_GET['quiz']);?>&quiz_id=<?php echo($_GET['quiz']);?>&user_id=<?php echo($_SESSION['id']);?>" class="text-lg">Results</a>
        </div>

        <div class="flex items-center gap-3">
            <!-- Profile Image and Share Button -->
            <div class="relative inline-flex items-center gap-2" id="shareContainer">
                <!-- Profile Image (opens user list modal) -->
                <div class="relative">
                    <img id="profileImage" src="../assets/profilepic/demo.jpg" alt="Profile"
                        class="w-10 h-10 rounded-full object-cover cursor-pointer">
                </div>

                <!-- Share Button (opens share modal) -->
                <button id="openShareModal"
                    class="bg-gray-200 px-4 py-1 rounded-full text-lg hover:bg-[#CFCFCF]">Share</button>
                <button type="submit" id="saveBtn"
                    class="bg-gray-200 px-4 py-1 rounded-full text-lg hover:bg-[#CFCFCF]">Save</button>
                <button onclick="window.location.href='../frontend/quizready.php?id=<?php echo $quiz_id; ?>'" class="bg-[#A435F0] text-white px-4 py-1 rounded-full text-lg hover:bg-purple-700">Present</button>
                <!-- Share Modal (invite user) -->
                <div id="shareModal"
                    class="absolute top-14 right-0 mt-2 w-80 bg-white shadow-lg border border-gray-200 rounded-xl p-4 z-50 hidden">
                    <h2 class="text-lg font-semibold mb-3">Share Quiz</h2>

                    <label class="block text-sm font-medium mb-1">User Email</label>
                    <input type="email" id="shareEmail" placeholder="example@mail.com"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <label class="block text-sm font-medium mb-1">Permission</label>
                    <select id="permissionSelect"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="edit">Can Edit</option>
                        <option value="view">Can View</option>
                    </select>

                    <div class="flex justify-end space-x-2">
                        <button id="cancelShare"
                            class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300 text-sm">Cancel</button>
                        <button id="confirmShare"
                            class="px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700 text-sm">Share</button>
                    </div>
                </div>

                <!-- Collaborators Modal -->
                <div id="collaboratorsModal"
                    class="absolute top-14 right-14 mt-2 w-72 bg-white shadow-lg border border-gray-200 rounded-xl p-4 z-50 hidden max-h-[300px] overflow-auto">
                    <h2 class="text-lg font-semibold mb-3">Collaborators</h2>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center justify-between">
                            <span>Alice (Owner)</span><span class="text-gray-500 text-xs">Full access</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>bob@example.com</span><span class="text-gray-500 text-xs">Can Edit</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>charlie@example.com</span><span class="text-gray-500 text-xs">Can View</span>
                        </li>
                    </ul>
                    <div class="flex justify-end mt-4">
                        <button id="closeCollaborators"
                            class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300 text-sm">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- slidebar -->
    <div class="flex flex-row bg-[#E5E5E5] h-full mt-8">
        <div class="flex flex-col w-[200px] bg-[#E5E5E5] mr-4 items-end slidebar">
            <!-- New Slide Button -->
            <button id="newSlideBtn"
                class="bg-[#A435F0] absolute text-white h-10 w-[170px] z-20 flex justify-center items-center rounded-full hover:bg-purple-700 mb-6">
                + New Slide
            </button>

            <!-- Slides Container -->
            <div id="slidesContainer" class="w-full mt-12">
                <!-- Slides will be added here dynamically -->
            </div>
        </div>

        <!-- Main Content Area -->
        <div id="mainContent"
            class="bg-white border-2 flex flex-col border-transparent hover:border-purple-300 ml-2 rounded-lg py-10 pl-12 pr-10 w-full max-w-4xl h-[500px] shadow-lg transition-all duration-200">
            <h1 id="questionText" class="text-3xl font-small text-black mb-8" contenteditable="true">Type your question
                here...</h1>

            <div id="contentArea">
                <!-- Content will change based on question type -->
                <h2 class="text-xl font-small text-black mt-12 mb-2">Options</h2>
                <div class="grid grid-cols-2 gap-6 pt-4">
                    <button
                        class="bg-red-500 text-black text-xl font-medium py-4 rounded-lg hover:bg-red-600 transition">Option1</button>
                    <button
                        class="bg-yellow-400 text-black text-xl font-medium py-4 rounded-lg hover:bg-yellow-500 transition">Option2</button>
                    <button
                        class="bg-sky-400 text-black text-xl font-medium py-4 rounded-lg hover:bg-sky-500 transition">Option3</button>
                    <button
                        class="bg-green-500 text-black text-xl font-medium py-4 rounded-lg hover:bg-green-600 transition">Option4</button>
                </div>
            </div>
        </div>

        <!-- Sidebar Controls -->
        <div
            class="w-[300px] h-[500px] ml-8 bg-white p-5 rounded-xl shadow-md relative font-sans text-sm overflow-y-auto">
            <h2 class="text-lg font-semibold mb-4">Slide</h2>

            <label class="text-xs font-medium text-gray-700 mb-1 block">Question Type</label>
            <select id="questionType"
                class="w-full bg-gray-100 p-2 rounded-md mb-4 text-sm text-gray-800 focus:outline-none">
                <option value="multiple">Multiple Choice</option>
                <option value="fillblank">Fill in the blank</option>
                <option value="shortanswer">Short Answer</option>
            </select>

            <div id="optionsContainer" class="mt-4">
                <!-- Options editing will appear here for multiple choice -->
            </div>

            <label class="text-xs font-medium text-gray-700 block mb-1">Image</label>
            <p class="text-xs text-gray-400 mb-2">We support .jpg, .png, .jpeg and .png</p>
            <div>
                <button id="openModalBtn" class="hover:cursor-pointer"><img src="../assets/images/draganddrop.png"
                        alt="dragndrop" class="h-8 w-auto rounded-md"></button>
            </div>

            <hr class="border-t border-purple-300 mb-4">

            <label class="text-xs font-medium text-gray-700 block mb-1">Text</label>
            <div class="flex items-center gap-2 mb-2">
                <label class="text-xs">Choose Text color</label>
                <input type="color" class="w-5 h-5 rounded-full border">
            </div>
            <div class="mb-4">
                <label class="text-xs block mb-1">Choose Font</label>
                <select class="border rounded-md px-2 py-1 w-full">
                    <option>OutFit</option>
                </select>
            </div>

            <label class="text-xs font-medium text-gray-700 block mb-1">Background</label>
            <div class="flex items-center gap-2 mb-2">
                <label class="text-xs">Choose Background Color</label>
                <input type="color" class="w-5 h-5 rounded-full border">
            </div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-xs">Choose Background Image</label>
                <button class="bg-gray-100 px-2 py-1 rounded-md text-sm">Add +</button>
            </div>

            <p class="text-xs underline text-gray-500 cursor-pointer">Reset default background</p>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-col h-1/2 gap-4 bg-white p-3 rounded-xl shadow w-[100px] ml-10">
            <button
                class="flex flex-col items-center justify-center bg-gray-100 border border-purple-300 rounded-lg py-2 hover:shadow-md hover:border-[#A435F0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5l3 3L12 15H9v-3L18.5 2.5z" />
                </svg>
                <span class="text-xs mt-1 text-gray-700">Edit</span>
            </button>

            <button
                class="flex flex-col items-center justify-center bg-gray-100 border border-purple-300 rounded-lg py-2 hover:shadow-md hover:border-[#A435F0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2h10z" />
                </svg>
                <span class="text-xs mt-1 text-gray-700">Delete</span>
            </button>

            <button
                class="flex flex-col items-center justify-center bg-gray-100 border border-purple-300 rounded-lg py-2 hover:shadow-md hover:border-[#A435F0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M12 6a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                </svg>
                <span class="text-xs mt-1 text-gray-700">Theme</span>
            </button>

            <button
                class="flex flex-col items-center justify-center bg-gray-100 border border-purple-300 rounded-lg py-2 hover:shadow-md hover:border-[#A435F0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path d="M4 3h9l5 5v13a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1zm9 0v5h5" />
                </svg>
                <span class="text-xs mt-1 text-gray-700">Template</span>
            </button>
        </div>
    </div>

    <!-- modal for uploading image. -->
    <div id="openModal" class="fixed inset-0 bg-black bg-opacity-40 flex hidden justify-center items-center z-50">
    <div class="bg-white rounded-2xl p-6 w-[600px] h-auto">
        <h2 class="text-xl font-semibold mb-4 text-center text-purple-700">Upload Image</h2>
        <div id="drop-area"
            class="p-8 border-2 border-dashed border-gray-300 rounded-xl text-center cursor-pointer transition hover:border-purple-500 hover:bg-purple-50"
            onclick="document.getElementById('fileElem').click()">
            <input type="file" id="fileElem" accept="image/*" class="hidden">
            <p class="text-gray-500 mb-2">Drag & drop an image here<br>or click to upload</p>
            <div id="preview" class="mt-4"></div>
        </div>
        <div class="flex justify-center mt-6">
            <button id="closeModalBtn"
                class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">Close</button>
        </div>
    </div>
</div>

    <script src="../assets/js/quiz.js"></script>
</body>

</html>