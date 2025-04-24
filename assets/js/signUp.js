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