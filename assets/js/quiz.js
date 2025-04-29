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

    const openModalBtn = document.getElementById("openModalBtn");
    const openModal = document.getElementById("openModal");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const dropArea = document.getElementById("drop-area");
    const fileInput = document.getElementById("fileElem");
    const preview = document.getElementById("preview");

    openModalBtn.addEventListener("click", () => {
        openModal.classList.remove("hidden");
        openModal.classList.add("flex");
    });

    fileInput.addEventListener("change", handleFiles);

    dropArea.addEventListener("click", () => fileInput.click());

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
    
    closeModalBtn.addEventListener("click", () => {
        openModal.classList.add("hidden");
    });
});