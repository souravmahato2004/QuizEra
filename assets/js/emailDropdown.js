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
