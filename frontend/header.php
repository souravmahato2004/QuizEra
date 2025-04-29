<?php 
    if(session_status()==PHP_SESSION_NONE){
        session_start();
    }
?>

<div class="flex text-white items-center justify-end py-2 w-full bg-white">
    <div class="flex justify-center items-center h-8 w-8 text-[#797979] border border-black-2 mr-2 rounded-lg">
        <i class="ri-notification-2-line"></i>
    </div>

    <div class="w-11/13 h-8 mr-2 text-lg">
        <input type="text" maxlength="6" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            placeholder="Enter Code"
            class="bg-white text-[#797979] h-8 rounded-lg border border-black-2 px-3 text-sm placeholder:text-[#797979] placeholder:align-middle">
    </div>
    <div class="mr-2 h-8">
        <p class="text-[#797979] h-8 rounded-lg w-11/13 px-2 border border-black-2 flex items-center">
            Get Help
        </p>
    </div>

    <!-- Wrapper for user icon and dropdown -->
    <div class="relative">
        <!-- User Icon -->
        <div id="quizUserIcon" class="flex justify-center items-center h-8 w-8 rounded-lg text-[#797979] border border-black-2 mr-2 cursor-pointer bg-white">
            <i class="ri-user-line"></i>
        </div>

        <!-- Dropdown Modal -->
        <div id="quizUserDropdown" class="absolute right-2 mt-2 w-64 bg-white rounded-xl shadow-lg p-4 hidden z-50">
            <div class="flex items-center space-x-3">
                <img src="../assets/profilepic/demo.jpg" alt="User" class="w-10 h-10 rounded-full">
                <div>
                    <h4 class="font-semibold text-gray-800"><?php echo $_SESSION['username'] ?></h4>
                    <p class="text-sm text-gray-500"><?php echo $_SESSION['email']?></p>
                </div>
            </div>
            <hr class="my-3 border-1 border-gray-300">
            <ul class="space-y-2 text-black">
                <li class="hover:underline cursor-pointer"><a href="profilePage.php">Profile</a></li>
                <li class="hover:underline cursor-pointer"><a href="accountSettings.php">Account Settings</a></li>
                <li class="hover:underline cursor-pointer"><a href="subscriptionPage.php">Pricing & Billing</a></li>
                <li class="hover:underline cursor-pointer"><button>Language</button></li>
                <li class="text-red-500 hover:underline cursor-pointer"><a href="../backend/logoutBackend.php">Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
    const quizUserIcon = document.getElementById('quizUserIcon');
    const quizUserDropdown = document.getElementById('quizUserDropdown');

    quizUserIcon.addEventListener('click', () => {
        quizUserDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!quizUserIcon.contains(e.target) && !quizUserDropdown.contains(e.target)) {
            quizUserDropdown.classList.add('hidden');
        }
    });
</script>
