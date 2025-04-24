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
