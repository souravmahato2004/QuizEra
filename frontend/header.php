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
        <form id="quizCodeForm" action="../backend/verifyQuizCode.php" method="POST" class="flex">
            <input type="text" name="code" maxlength="6" inputmode="numeric" 
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   placeholder="Enter Code"
                   class="bg-white text-[#797979] h-8 rounded-lg border border-black-2 px-3 text-sm placeholder:text-[#797979] placeholder:align-middle"
                   required>
            <button type="submit" class="ml-2 px-3 h-8 bg-[#9D3AE3] text-white rounded-lg hover:bg-[#8a2be2]">
                Join
            </button>
        </form>
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
                <img src="../assets/profilepic/<?php echo $_SESSION['profile_pic'] ?? 'demo.jpg'; ?>" alt="User" class="w-10 h-10 rounded-full">
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
    // Handle user dropdown toggle
document.getElementById('quizUserIcon').addEventListener('click', function(e) {
    e.stopPropagation(); // Prevent event from bubbling up
    const dropdown = document.getElementById('quizUserDropdown');
    dropdown.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('quizUserDropdown');
    const userIcon = document.getElementById('quizUserIcon');
    
    if (!dropdown.contains(e.target) && !userIcon.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

    // Handle quiz code submission
    quizCodeForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const code = this.elements['code'].value.trim();
        
        if (code.length !== 6) {
            alert('Please enter a valid 6-digit code');
            return;
        }
        
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Joining...';
        
        // Verify the code exists before redirecting
        try {
            const response = await fetch('../backend/verifyQuizCode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `code=${code}`
            });
            const data = await response.json();
            
            if (data.valid) {
                // alert("hello");
                // Check if there's a specific redirect URL provided by the backend
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    // Fallback to the default redirect
                    window.location.href = `quizready.php?session_code=${code}`;
                }
            } else {
                // Show error message
                alert(data.message || 'Invalid quiz code. Please check the code and try again.');
                
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        } catch (error) {
            console.error('Error verifying quiz code:', error);
            alert('Failed to verify quiz code. Please try again.');
            
            // Reset button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    });
</script>