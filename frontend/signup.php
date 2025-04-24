<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SignUp</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
  <style>
    .font-outfit {
      font-family: 'Outfit', sans-serif;
    }
  </style>
</head>
<body class="bg-[#E1B6FF] flex items-center justify-center min-h-screen font-outfit">

  <!-- Modal for OTP -->
  <div id="otpModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-sm">
      <h2 class="text-2xl font-semibold mb-4">Enter OTP</h2>
      <p class="text-gray-600 mb-4">We've sent an OTP to your Email</p>
      <input type="text" maxlength="6" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"placeholder="Enter your 6-Digit OTP" class="w-full border border-gray-300 p-2 rounded-md mb-4 outline-none focus:ring-2 focus:ring-[#A435F0]" />
      <div class="flex justify-end gap-3">
        <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md">Cancel</button>
        <button onclick="submitOtp()" class="px-4 py-2 bg-[#A435F0] text-white rounded-md">Submit</button>
      </div>
    </div>
  </div>

  <!-- Error Modal -->
  <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-sm">
      <h2 class="text-2xl font-semibold mb-4 text-red-600">Error</h2>
      <p id="errorText" class="text-gray-700 mb-4"></p>
      <div class="flex justify-end">
        <button onclick="closeErrorModal()" class="px-4 py-2 bg-red-500 text-white rounded-md">Close</button>
      </div>
    </div>
  </div>

  <!-- Result Modal -->
  <div id="resultModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-sm">
      <h2 id="resultTitle" class="text-2xl font-semibold mb-4"></h2>
      <p id="resultMessage" class="text-gray-700 mb-4"></p>
      <div class="flex justify-end">
        <button onclick="closeResultModal()" class="px-4 py-2 bg-[#A435F0] text-white rounded-md">OK</button>
      </div>
    </div>
  </div>


  <div class="relative w-full max-w-5xl h-[600px] bg-white rounded-3xl overflow-hidden shadow-xl">
    <!-- Back Button -->
    <button onclick="history.back()" class="absolute top-10 left-10 text-black z-20">
      <div class="bg-[#D9D9D9] px-2 py-1 rounded-full">
        <i class="ri-arrow-left-long-line"></i>
      </div>
    </button>

    <!-- Graphic -->
    <div class="absolute top-0 right-0 w-1/3 h-full hidden lg:block z-0">
      <img src="../assets/images/signup.svg" alt="Signup Graphic" class="w-full h-full object-cover" />
    </div>

    <!-- Header toggle -->
    <div class="absolute top-10 right-[36%] z-20 text-sm flex items-center gap-2">
      <span id="toggleText" class="text-[#727272] font-medium text-lg">Already an User?</span>
      <span id="toggleLink" class="text-[#A435F0] font-medium text-lg underline cursor-pointer">Sign in</span>
    </div>

    <!-- Panel container -->
    <div class="relative w-2/3 h-full mt-[30px] ml-[40px] overflow-hidden z-10">
      <div id="panelContainer" class="absolute top-0 left-0 flex w-[200%] h-full transform transition-transform duration-500 ease-in-out">

        <!-- Sign Up Section -->
        <div class="w-1/2 flex">
          <div class="w-4/5 px-10 py-16">
            <h2 class="text-6xl font-medium mb-2">Sign Up</h2>
            <p class="text-[#727272] text-xl mb-6">Create your QuizEra account for free...</p>

            <div class="relative mb-4">
              <i class="ri-user-line absolute left-3 top-1/2 transform -translate-y-1/2 text-[#727272] text-lg"></i>
              <input type="text" placeholder="Enter your Name"
                class="w-full border-b-2 border-black p-2 pl-10 outline-none placeholder:text-[#727272] text-lg" />
            </div>
            <div class="relative mb-4">
              <i class="ri-mail-line absolute left-3 top-1/2 transform -translate-y-1/2 text-[#727272] text-lg"></i>
              <input type="email" placeholder="Enter your Email"
                class="w-full border-b-2 border-black p-2 pl-10 outline-none placeholder:text-[#727272] text-lg" />
            </div>
            <div class="relative mb-2">
              <i class="ri-key-line absolute left-3 top-1/2 transform -translate-y-1/2 text-[#727272] text-lg"></i>
              <input type="password" id="password" placeholder="Enter your Password"
                class="w-full border-b-2 border-black p-2 pl-10 outline-none placeholder:text-[#727272] text-lg" />
              <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#727272]">
                <i class="ri-eye-line text-lg"></i>
              </button>
            </div>

            <p class="text-sm ml-4 text-[#727272]" id="passwordRequirements">
              <i class="ri-circle-fill text-[10px]"></i> Least 8 characters<br/>
              <i class="ri-circle-fill text-[10px]"></i> At least one number and one special character<br />
              <i class="ri-circle-fill text-[10px]"></i> Both lowercase and uppercase letters
            </p>

            <div class="relative mb-2">
              <i class="ri-key-line absolute left-3 top-1/2 transform -translate-y-1/2 text-[#727272] text-lg"></i>
              <input type="password" id="reEnterPassword" placeholder="Re-Type Password"
                class="w-full border-b-2 border-black p-2 pl-10 outline-none placeholder:text-[#727272] text-lg" />
              <button type="button" id="toggleReEnterPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#727272]">
                <i class="ri-eye-line text-lg"></i>
              </button>
            </div>

            <div class="flex justify-start items-center gap-[10px]">
              <button onclick="validateSignUp()" class="bg-[#A435F0] text-white px-6 py-2 rounded-full">Sign Up</button>
              <span class="text-[#727272] text-lg ml-4">Or</span>
              <div class="w-fit mr-2">
                <button class="flex items-center bg-gray-200 rounded-full px-2 py-2 w-full">
                  <img src="../assets/logo/google.png" alt="Google Logo" class="w-6 h-6" />
                </button>
              </div>
              <div class="w-fit">
                <button class="flex items-center bg-gray-200 rounded-md px-2 py-2 w-full">
                  <img src="../assets/logo/microsoft.png" alt="Microsoft Logo" class="w-6 h-6" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Sign In Section (unchanged) -->
        <div class="w-1/2 flex">
          <div class="w-4/5 px-10 py-16">
            <h2 class="text-6xl font-medium mb-2">Sign In</h2>
            <p class="text-[#727272] text-xl mb-6">Login to your QuizEra account...</p>
            <div class="relative mb-4">
              <i class="ri-mail-line absolute left-3 top-1/2 transform -translate-y-1/2 text-[#727272] text-lg"></i>
              <input type="email" placeholder="Enter your Email"
                class="w-full border-b-2 border-black p-2 pl-10 outline-none placeholder:text-[#727272] text-lg" />
            </div>
            <div class="relative mb-2">
              <i class="ri-key-line absolute left-3 top-1/2 transform -translate-y-1/2 text-[#727272] text-lg"></i>
              <input type="password" id="loginPassword" placeholder="Enter your Password"
                class="w-full border-b-2 border-black p-2 pl-10 outline-none placeholder:text-[#727272] text-lg" />
              <button type="button" id="toggleLoginPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#727272]">
                <i class="ri-eye-line text-lg"></i>
              </button>
            </div>
            <a href="#" class="text-sm text-black">Forgot your Password?</a>
            <div class="mt-4">
              <button class="bg-[#A435F0] text-white px-6 py-2 rounded-full">Sign In</button>
              <span class="text-[#727272] text-lg ml-4">Or</span>
            </div>
            <div class="space-y-4 w-fit max-w-xs mt-4">
              <button class="flex items-center gap-3 bg-gray-200 rounded-full px-4 py-2 w-full">
                <img src="../assets/logo/google.png" alt="Google Logo" class="w-6 h-6" />
                <span class="text-[#727272] font-medium">Login with Google</span>
              </button>
              <button class="flex items-center gap-3 bg-gray-200 rounded-md px-4 py-2 w-full">
                <img src="../assets/logo/microsoft.png" alt="Microsoft Logo" class="w-6 h-6" />
                <span class="text-[#727272] font-medium">Login with Microsoft</span>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

<script>
  // toggle signup and signin
  const toggleLink = document.getElementById("toggleLink");
  const toggleText = document.getElementById("toggleText");
  const panelContainer = document.getElementById("panelContainer");

  toggleLink.addEventListener("click", () => {
    if (panelContainer.style.transform === "translateX(-50%)") {
      panelContainer.style.transform = "translateX(0)";
      toggleText.textContent = "Already an User?";
      toggleLink.textContent = "Sign in";
    } else {
      panelContainer.style.transform = "translateX(-50%)";
      toggleText.textContent = "Don't have an account?";
      toggleLink.textContent = "Sign up";
    }
  });
// password mathing and criteria
  const passwordInput = document.getElementById("password");
  const reEnterPasswordInput = document.getElementById("reEnterPassword");
  const passwordRequirements = document.getElementById("passwordRequirements");

  document.getElementById("togglePassword").addEventListener("click", function () {
    const icon = this.querySelector("i");
    passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    icon.classList.toggle("ri-eye-line");
    icon.classList.toggle("ri-eye-off-line");
  });

  document.getElementById("toggleReEnterPassword").addEventListener("click", function () {
    const icon = this.querySelector("i");
    reEnterPasswordInput.type = reEnterPasswordInput.type === "password" ? "text" : "password";
    icon.classList.toggle("ri-eye-line");
    icon.classList.toggle("ri-eye-off-line");
  });

  document.getElementById("toggleLoginPassword").addEventListener("click", function () {
    const icon = this.querySelector("i");
    const loginPassword = document.getElementById("loginPassword");
    loginPassword.type = loginPassword.type === "password" ? "text" : "password";
    icon.classList.toggle("ri-eye-line");
    icon.classList.toggle("ri-eye-off-line");
  });

  passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;
    const icons = passwordRequirements.querySelectorAll("i");

    const isLengthValid = password.length >= 8;
    const hasNumberAndSpecial = /\d/.test(password) && /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const hasLowerAndUpper = /[a-z]/.test(password) && /[A-Z]/.test(password);

    function setRequirement(index, isValid) {
      icons[index].className = isValid ? "ri-check-line text-[10px]" : "ri-circle-fill text-[10px]";
      icons[index].parentNode.style.color = isValid ? "green" : "#727272";
    }

    setRequirement(0, isLengthValid);
    setRequirement(1, hasNumberAndSpecial);
    setRequirement(2, hasLowerAndUpper);

    passwordRequirements.style.color = (isLengthValid && hasNumberAndSpecial && hasLowerAndUpper) ? "green" : "#727272";
  });

  function validateReEnterPassword() {
  const reEntered = reEnterPasswordInput.value;
  const original = passwordInput.value;

  if (!reEntered) {
    reEnterPasswordInput.classList.remove("bg-green-100", "bg-red-100");
  } else if (reEntered === original) {
    reEnterPasswordInput.classList.add("bg-green-100");
    reEnterPasswordInput.classList.remove("bg-red-100");
  } else {
    reEnterPasswordInput.classList.add("bg-red-100");
    reEnterPasswordInput.classList.remove("bg-green-100");
  }
}

reEnterPasswordInput.addEventListener("input", validateReEnterPassword);
passwordInput.addEventListener("input", validateReEnterPassword);

  function validateSignUp() {
    const password = passwordInput.value;
    const rePassword = reEnterPasswordInput.value;

    const isLengthValid = password.length >= 8;
    const hasNumberAndSpecial = /\d/.test(password) && /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const hasLowerAndUpper = /[a-z]/.test(password) && /[A-Z]/.test(password);
    const isPasswordMatch = password === rePassword;

    let errorMessages = [];

    if (!isLengthValid) errorMessages.push("Password must be at least 8 characters long.");
    if (!hasNumberAndSpecial) errorMessages.push("Password must include at least one number and one special character.");
    if (!hasLowerAndUpper) errorMessages.push("Password must contain both lowercase and uppercase letters.");
    if (!isPasswordMatch) errorMessages.push("Passwords do not match.");

    if (errorMessages.length > 0) {
      showErrorModal(errorMessages.join("\n"));
    } else {
      showOtpModal();
    }
  }


  function showOtpModal() {
    document.getElementById("otpModal").classList.remove("hidden");
  }

  function closeModal() {
    document.getElementById("otpModal").classList.add("hidden");
  }

  function submitOtp() {
    const otpInput = document.querySelector("#otpModal input");
    const otp = otpInput.value.trim();

    if (otp.length === 6 && /^\d{6}$/.test(otp)) {
      closeModal();
      showResultModal("OTP Verified", "You have successfully signed up!", true);
    } else {
      closeModal();
      showResultModal("Invalid OTP", "Please enter a valid 6-digit numeric OTP.", false);
    }
  }


  // show error model
  function showErrorModal(message) {
    document.getElementById("errorText").textContent = message;
    document.getElementById("errorModal").classList.remove("hidden");
  }

  function closeErrorModal() {
    document.getElementById("errorModal").classList.add("hidden");
  }

  // show otp model
  function showResultModal(title, message, success = true) {
    const modal = document.getElementById("resultModal");
    const titleEl = document.getElementById("resultTitle");
    const messageEl = document.getElementById("resultMessage");

    titleEl.textContent = title;
    titleEl.className = success ? "text-2xl font-semibold mb-4 text-green-600" : "text-2xl font-semibold mb-4 text-red-600";
    messageEl.textContent = message;
    modal.classList.remove("hidden");
  }

  function closeResultModal() {
    document.getElementById("resultModal").classList.add("hidden");
  }

</script>

</body>
</html>
