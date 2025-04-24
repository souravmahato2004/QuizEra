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