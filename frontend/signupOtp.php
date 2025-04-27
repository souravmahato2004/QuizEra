<?php 
session_start(); 
include '../backend/otpverification.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
    <style>
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
    </style>
</head>
<body class="bg-[#E1B6FF] flex items-center justify-center min-h-screen font-outfit">

    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-sm">
        <h2 class="text-2xl font-semibold mb-4">Enter OTP</h2>
        <p class="text-gray-600 mb-4">We've sent an OTP to your Email</p>

        <?php if (isset($error_message)): ?>
        <p class="text-red-600 mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="otp" maxlength="6" inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Enter your 6-Digit OTP"
                class="w-full border border-gray-300 p-2 rounded-md mb-4 outline-none focus:ring-2 focus:ring-[#A435F0]" required />

            <div class="flex justify-end gap-3">
                <button type="button" onclick="window.history.back()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md">Cancel</button>
                <button type="submit" name="submitOtp" class="px-4 py-2 bg-[#A435F0] text-white rounded-md">Submit</button>
            </div>
        </form>
    </div>

</body>
</html>
