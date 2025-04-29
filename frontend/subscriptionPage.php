<?php 
    if(session_status()==PHP_SESSION_NONE){
        session_start();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Billing and Pricing</title>
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

    <?php include '../frontend/sidebar.php'?>

    <div class="bg-white w-[1077px] ml-[363px] flex-1 overflow-y-auto">
        <?php include '../frontend/header.php'?>

        <div class="flex flex-row justify-center items-center bg-[#F0D9FF] mb-4 py-14">
            <div class="h-[300px] w-[300px] rounded-full border-2 border-gray-500 mr-14">
                <img src="../assets/profilepic/demo.jpg" alt="demo" class="rounded-full">
            </div>
            <div class="flex flex-col">
                <h1 class="text-7xl font-medium text-[#9D3AE3] pb-4">Welcome,<br><?php echo $_SESSION['username'] ?></h1>
                <p class="text-2xl font-small pl-2"><?php echo $_SESSION['email'] ?></p>
            </div>
        </div>

        <div class="flex flex-col pl-4 pr-8 pb-16 ml-8 mt-12">
            <h2 class="text-4xl font-medium mb-4 text-[#9D3AE3] mb-4">Your Current Plan</h2>
            <div class="bg-[#F3F3F3] h-auto w-2/3 pl-4 pt-2 rounded-xl border-2 border-[#DBDBDB] hover:border-[#797979] transition-all duration-200">
                <span class="text-[#9D3AE3] text-xl">Free Plan</span>
                <span class="text-xl">: Perfect for casual users and quiz enthusiasts just getting started.</span>
                <ul class="list-disc pl-8 my-4">
                    <li>✅ Accesss to public quizes</li>
                    <li>✅ Create upto 5 quizes (10 questions each)</li>
                    <li>✅ Basic question types: MCQs, True/False</li>
                    <li>✅ Basic quiz analytics</li>
                    <li>✅ Personal dashboard for tracking scores</li>
                    <li>🚫 Ad-free experience</li>
                </ul>
            </div>
            <hr class="h-[1.5px] w-11/13 bg-[#9D3AE3] mt-10">
            <h2 class="text-4xl font-medium mt-8 mb-4 text-[#9D3AE3] mb-4">Upgrade your Plan</h2>
            <div class="flex flex-row space-x-8">
            <!-- Card 1 -->
            <div class="flex flex-col justify-between bg-[#F3F3F3] h-[350px] w-3/5 pl-4 pt-2 rounded-xl border-2 border-[#DBDBDB] hover:border-[#797979] transition-all duration-200">
                <div>
                    <span class="text-[#9D3AE3] text-xl">Professional Plan</span>
                    <span class="text-xl">: Ideal for educators, trainers, and power users who need more control and customization.</span>
                    <ul class="list-disc pl-8 my-4">
                        <li>✨ All Free Plan features, plus:</li>
                        <li>✅ Create unlimited quizzes</li>
                        <li>✅ Add up to 50 questions per quiz</li>
                        <li>✅ Advanced question types: Fill-in-the-blank, Match the pairs, Timed questions</li>
                        <li>✅ Advanced analytics: question-wise performance, user stats</li>
                        <li>✅ Ad-free experience</li>
                    </ul>
                </div>
                <button class="self-center py-2 mb-4 text-white bg-[#9D3AE3] w-1/3 rounded-3xl hover:bg-purple-700 transition duration-500">
                    50% OFF&nbsp;
                    <span class="relative inline-block text-white">
                        138
                        <span class="absolute left-0 top-1/2 w-full h-[2px] bg-white rotate-[135deg]"></span>
                    </span>
                    &nbsp;69/-
                </button>
            </div>

            <!-- Card 2 -->
            <div class="flex flex-col justify-between bg-[#F3F3F3] h-[350px] w-3/5 pl-4 pt-2 rounded-xl border-2 border-[#DBDBDB] hover:border-[#797979] transition-all duration-200">
                <div>
                    <span class="text-[#9D3AE3] text-xl">Community Plan</span>
                    <span class="text-xl">: Built for institutions, clubs, and groups who want to grow, collaborate, and host engaging quiz events.</span>
                    <ul class="list-disc pl-8 my-4">
                        <li>✨ All Professional Plan features, plus:</li>
                        <li>✅ Host live quiz events with leaderboard</li>
                        <li>✅ Set up custom domains for your quiz portal</li>
                        <li>✅ Create and manage quiz communities with member roles</li>
                        <li>✅ Publish featured quizzes to the community spotlight</li>
                        <li>✅ Access to beta features and early updates</li>
                        <li>✅ Dedicated account manager & priority feedback loop</li>
                    </ul>
                </div>
                <button class="self-center py-2 mb-4 text-white bg-[#9D3AE3] w-1/3 rounded-3xl hover:bg-purple-700 transition duration-500">
                    50% OFF&nbsp;
                    <span class="relative inline-block text-white">
                        258
                        <span class="absolute left-0 top-1/2 w-full h-[2px] bg-white rotate-[135deg]"></span>
                    </span>
                    &nbsp;119/-
                </button>
            </div>
        </div>
        </div>
    </div>
    </div>

</body>

</html>