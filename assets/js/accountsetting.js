// email drop down
const emailBtn = document.getElementById("emailDropdownBtn");
const emailDropdown = document.getElementById("emailDropdown");

emailBtn.addEventListener("click", () => {
  emailDropdown.classList.toggle("hidden");
});

window.addEventListener("click", (e) => {
  if (
    !emailDropdown.contains(e.target) &&
    !emailBtn.contains(e.target)
  ) {
    emailDropdown.classList.add("hidden");
  }
});

// passowrd dropdown

const passwordBtn = document.getElementById("passwordDropdownBtn");
const passwordDropdown = document.getElementById("passwordDropdown");

passwordBtn.addEventListener("click", () => {
  passwordDropdown.classList.toggle("hidden");
});

window.addEventListener("click", (e) => {
  if (
    !passwordDropdown.contains(e.target) &&
    !passwordBtn.contains(e.target)
  ) {
    passwordDropdown.classList.add("hidden");
  }
});

// phone number dropdown

const phoneBtn = document.getElementById("phoneDropdownBtn");
const phoneDropdown = document.getElementById("phoneDropdown");

phoneBtn.addEventListener("click", () => {
  phoneDropdown.classList.toggle("hidden");
});

window.addEventListener("click", (e) => {
  if (
    !phoneDropdown.contains(e.target) &&
    !phoneBtn.contains(e.target)
  ) {
    phoneDropdown.classList.add("hidden");
  }
});

// username dropdown

const userDropdownBtn = document.getElementById("userDropdownBtn");
const userDropdown = document.getElementById("userDropdown");
const openModalBtn = document.getElementById("openModalBtn");
const openModal = document.getElementById("openModal");
const closeModalBtn = document.getElementById("closeModalBtn");
const dropArea = document.getElementById("drop-area");
const fileInput = document.getElementById("fileElem");
const preview = document.getElementById("preview");

userDropdownBtn.addEventListener("click", () => {
  userDropdown.classList.toggle("hidden");
});

openModalBtn.addEventListener("click", () => {
  openModal.classList.remove("hidden");
  openModal.classList.add("flex");
});

closeModalBtn.addEventListener("click", () => {
  openModal.classList.add("hidden");
});

dropArea.addEventListener("click", () => fileInput.click());

fileInput.addEventListener("change", handleFiles);

dropArea.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropArea.classList.add("border-blue-500", "bg-blue-50");
});

dropArea.addEventListener("dragleave", () => {
  dropArea.classList.remove("border-blue-500", "bg-blue-50");
});

dropArea.addEventListener("drop", (e) => {
  e.preventDefault();
  dropArea.classList.remove("border-blue-500", "bg-blue-50");
  if (e.dataTransfer.files.length) {
    fileInput.files = e.dataTransfer.files;
    handleFiles();
  }
});

function handleFiles() {
  const file = fileInput.files[0];
  if (file && file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.onload = () => {
      preview.innerHTML = `<img src="${reader.result}" class="rounded-lg max-h-52 mx-auto" />`;
    };
    reader.readAsDataURL(file);
  } else {
    preview.innerHTML = `<p class="text-red-500 mt-2">Please upload a valid image.</p>`;
  }
}

window.addEventListener("click", (e) => {
  if (!userDropdown.contains(e.target) && !userDropdownBtn.contains(e.target)) {
    userDropdown.classList.add("hidden");
  }
});