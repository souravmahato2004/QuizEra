<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet" />
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
                <h1 class="text-7xl font-medium text-[#9D3AE3] pb-4">Welcome,<br>QuizEra UserName</h1>
                <p class="text-2xl font-small pl-2">quizerauser123@gmail.com</p>
            </div>
        </div>

        <div class="px-4 pb-16">
            <h2 class="text-xl font-small mb-2">Your Quizzes</h2>
            <p class="flex items-center justify-center font-medium text-black py-4">Looks like you haven't worked on anything yet. Time to create a one Now!</p>
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
    </div>
    </div>

</body>

</html>