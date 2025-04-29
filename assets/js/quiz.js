document.addEventListener("DOMContentLoaded", () => {
    const shareModal = document.getElementById("shareModal");
    const collaboratorsModal = document.getElementById("collaboratorsModal");
    const openShareBtn = document.getElementById("openShareModal");
    const profileImage = document.getElementById("profileImage");
    const cancelShareBtn = document.getElementById("cancelShare");
    const closeCollaboratorsBtn = document.getElementById("closeCollaborators");
    const container = document.getElementById("shareContainer");

    // Share Modal
    openShareBtn.addEventListener("click", () => {
        shareModal.classList.toggle("hidden");
        collaboratorsModal.classList.add("hidden");
    });

    cancelShareBtn.addEventListener("click", () => {
        shareModal.classList.add("hidden");
    });

    // Collaborators Modal
    profileImage.addEventListener("click", () => {
        collaboratorsModal.classList.toggle("hidden");
        shareModal.classList.add("hidden");
    });

    closeCollaboratorsBtn.addEventListener("click", () => {
        collaboratorsModal.classList.add("hidden");
    });

    // Close modals when clicking outside
    document.addEventListener("click", (e) => {
        if (!container.contains(e.target)) {
            shareModal.classList.add("hidden");
            collaboratorsModal.classList.add("hidden");
        }
    });
});
