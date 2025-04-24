document.addEventListener('DOMContentLoaded', function () {
    const dropdownButtons = document.querySelectorAll('button[id^="dropdown"]');
    const dropdowns = document.querySelectorAll('div[id^="dropdown"]');

    // Close all dropdowns
    function closeAllDropdowns() {
        dropdowns.forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }

    // Toggle dropdown visibility
    function toggleDropdown(event, dropdown) {
        const isOpen = !dropdown.classList.contains('hidden');
        closeAllDropdowns();
        if (!isOpen) {
            dropdown.classList.remove('hidden');
        }
        event.stopPropagation();
    }

    dropdownButtons.forEach(button => {
        const dropdownId = button.id.replace('-btn', '');
        const dropdown = document.getElementById(dropdownId);

        // Show dropdown on hover over button
        button.addEventListener('mouseover', function () {
            closeAllDropdowns();
            dropdown.classList.remove('hidden');
        });

        // Hide dropdown when leaving button
        button.addEventListener('mouseleave', function () {
            setTimeout(() => {
                if (!dropdown.matches(':hover') && !button.matches(':hover')) {
                    dropdown.classList.add('hidden');
                }
            }, 100);
        });

        // Also hide dropdown when leaving the dropdown itself
        dropdown.addEventListener('mouseleave', function () {
            setTimeout(() => {
                if (!dropdown.matches(':hover') && !button.matches(':hover')) {
                    dropdown.classList.add('hidden');
                }
            }, 100);
        });

        // Toggle dropdown on click
        button.addEventListener('click', function (event) {
            toggleDropdown(event, dropdown);
        });
    });

    // Close dropdown if clicking outside
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.relative')) {
            closeAllDropdowns();
        }
    });
});
