<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account Settings</title>
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

<?php include '../backend/updateDetails.php' ?>

<body class="bg-gray-100 h-screen w-screen overflow-hidden flex font-outfit">

    <?php include '../frontend/sidebar.php' ?>

    <div class="bg-white w-full ml-[363px] flex flex-col overflow-y-auto">
        <?php include '../frontend/header.php' ?>

        <div class="flex flex-col mx-12 pb-16 pb-4 border-b border-[#9D3AE3]">
            <h2 class="text-4xl font-medium text-[#9D3AE3]">Account Settings</h2>

            <div class="relative mx-32 mt-10">
                <button id="userDropdownBtn" class="text-lg font-medium">Name & Image</button>
                <p class="text-sm text-[#797979]">You logged in as <?php echo $_SESSION['username'] ?></p>
                <div id="userDropdown" class="hidden mt-2 w-[648px] bg-white border rounded-lg shadow-lg p-4 z-10">
                    <form method="POST" class="flex flex-col" enctype="multipart/form-data"
                        action="../backend/updateDetails.php">
                        <input type="hidden" name="form_name" value="nameSection">

                        <label class="block text-md font-medium mb-2">UserName</label>
                        <input type="text" placeholder="QuizEra Username" name="username"
                            value="<?php echo $_SESSION['username'] ?>"
                            class="w-3/4 border-2 border-gray-300 rounded-md px-2 py-1 mb-4">

                        <label class="block text-md font-medium mb-2">Name</label>
                        <input type="text" placeholder="QuizEra name" name="Name"
                            value="<?php echo $_SESSION['name'] ?>"
                            class="w-3/4 border-2 border-gray-300 rounded-md px-2 py-1 mb-4">

                        <label class="block text-md font-medium mb-2">Profile Image</label>
                        <div class="flex items-start gap-2">
                            <div>
                                <input type="file" name="user_pic" id="fileInput" accept="image/*" hidden>
                                <button type="button" id="openModalBtn">
                                    <img src="../assets/images/draganddrop.png" alt="dragndrop" class="h-10 w-auto rounded-md">
                                </button>
                                <span id="fileName" class="block text-sm text-gray-600 mt-2"></span>
                            </div>
                            <div>
                                <?php if (!empty($profilePic)): ?>
                                    <img src="../assets/profilepic/<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture"
                                        class="h-10 w-10 rounded-full object-cover border border-gray-300">
                                <?php else: ?>
                                    <p class="text-sm text-gray-500 mt-1">No profile picture</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button type="submit"
                            class="bg-[#797979] text-white rounded-2xl w-fit px-4 py-1 hover:bg-gray-800 transition duration-400 mt-4">Save
                            Changes</button>
                    </form>
                </div>
            </div>

            <div class="relative mx-32 mt-10">
                <button id="emailDropdownBtn" class="text-lg font-medium">Email</button>
                <p class="text-sm text-[#797979]">your email is <?php echo $_SESSION['email'] ?></p>
                <div id="emailDropdown" class="hidden mt-2 w-[648px] bg-white border rounded-lg shadow-lg p-4 z-10">
                    <label class="block text-lg font-medium"><?php echo $_SESSION['email'] ?></label>
                </div>
            </div>
            <div class="relative mx-32 mt-10">
                <button id="passwordDropdownBtn" class="text-lg font-medium">Password</button>
                <p class="text-sm text-[#797979]">Forgot your password?Change now</p>
                <div id="passwordDropdown" class="hidden mt-2 w-[648px] bg-white border rounded-lg shadow-lg p-4 z-10">
                    <label class="block text-md font-medium mb-2">Enter Password</label>
                    <input type="text" placeholder="xxxxxxxxxxx"
                        class="w-3/4 border-2 border-gray-300 rounded-md px-2 py-1 mb-4">
                    <label class="block text-md font-medium mb-2">Re-enter Password</label>
                    <input type="text" placeholder="xxxxxxxxxxx"
                        class="w-3/4 border-2 border-gray-300 rounded-md px-2 py-1 mb-4">
                    <button
                        class="bg-[#797979] text-white rounded-2xl px-4 py-1 hover:bg-gray-800 transition duration-400 mb-4">Send
                        Code</button>
                </div>
            </div>
            <div class="relative mx-32 mt-10">
                <button id="phoneDropdownBtn" class="text-lg font-medium">Phone no.</button>
                <p class="text-sm text-[#797979]">your registered phone number is <?php echo $_SESSION['phoneno'] ?></p>
                <div id="phoneDropdown" class="hidden mt-2 w-[648px] bg-white border rounded-lg shadow-lg p-4 z-10">
                    <label class="block text-md font-medium mb-2">Enter Phone no.</label>
                    <input type="text" placeholder="+91-923xx-xxxx0" value="+91-"
                        class="w-3/4 border-2 border-gray-300 rounded-md px-2 py-1 mb-4">
                    <button
                        class="bg-[#797979] text-white rounded-2xl px-4 py-1 hover:bg-gray-800 transition duration-400 mb-4">Send
                        Code</button>
                </div>
            </div>
        </div>
        <div class="ml-36 mt-8 px-8">
            <h2 class="text-lg font-medium">Log out from everywhere</h2>
            <p class="text-sm text-[#797979] pb-4">This will log you out from all the devices you have logged in including
                this device</p>
            <a href="../backend/logoutBackend.php" class="bg-[#FFDADA] text-[#D90000] rounded-full px-4 py-2 hover:bg-red-300 transition duration-400 mb-4 mt-2">Log
                out</a>
        </div>
        <div class="ml-36 my-8 px-8">
            <h2 class="text-lg font-medium">Delete your QuizEra Account</h2>
            <p class="text-sm text-[#797979]">This will permanently delete your QuizEra account.All your quizzes and
                levels will be gone.</p>
            <button id="openDeleteModal"
                class="bg-[#EFDAFE] text-[#9700FF] rounded-2xl px-4 py-1 hover:bg-purple-300 transition duration-400 mb-4 mt-2">Delete</button>

        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
            <h2 class="text-lg font-semibold mb-2 text-red-600">Are you sure?</h2>
            <p class="text-sm text-gray-700 mb-4">
                Deleting your QuizEra account will permanently erase your quizzes, levels, and progress. This action cannot be undone.
            </p>
            <div class="flex justify-end gap-4">
                <button id="cancelDelete"
                    class="px-4 py-1 rounded-md border border-gray-400 hover:bg-gray-100">Cancel</button>
                <form method="POST" action="../backend/deleteQuizBackend.php">
                    <button type="submit"
                        class="px-4 py-1 bg-red-500 text-white rounded-md hover:bg-red-600">Delete</button>
                </form>
            </div>
        </div>
    </div>

</body>
<script src="../assets/js/accountsetting.js"></script>
<script>
    // Trigger file input on image button click
    document.getElementById('openModalBtn').addEventListener('click', () => {
        document.getElementById('fileInput').click();
    });

    // Display file name after file selection
    document.getElementById('fileInput').addEventListener('change', function () {
        const fileNameSpan = document.getElementById('fileName');
        fileNameSpan.textContent = this.files.length > 0 ? this.files[0].name : '';
    });

    document.getElementById('openDeleteModal').addEventListener('click', function () {
        document.getElementById('deleteModal').classList.remove('hidden');
    });

    document.getElementById('cancelDelete').addEventListener('click', function () {
        document.getElementById('deleteModal').classList.add('hidden');
    });
</script>
</html>