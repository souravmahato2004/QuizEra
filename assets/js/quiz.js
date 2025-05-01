document.addEventListener("DOMContentLoaded", () => {
  // ======================
  // STATE MANAGEMENT
  // ======================
  let currentSlide = null;
  let slides = [];
  let draggedSlide = null;
  let dragStartIndex = -1;

  // ======================
  // DOM ELEMENTS
  // ======================
  const dom = {
    newSlideBtn: document.getElementById("newSlideBtn"),
    slidesContainer: document.getElementById("slidesContainer"),
    questionType: document.getElementById("questionType"),
    mainContent: document.getElementById("mainContent"),
    contentArea: document.getElementById("contentArea"),
    questionText: document.getElementById("questionText"),
    shareModal: document.getElementById("shareModal"),
    collaboratorsModal: document.getElementById("collaboratorsModal"),
    openShareBtn: document.getElementById("openShareModal"),
    profileImage: document.getElementById("profileImage"),
    cancelShareBtn: document.getElementById("cancelShare"),
    closeCollaboratorsBtn: document.getElementById("closeCollaborators"),
    shareContainer: document.getElementById("shareContainer"),
    openModalBtn: document.getElementById("openModalBtn"),
    imageModal: document.getElementById("openModal"),
    closeModalBtn: document.getElementById("closeModalBtn"),
    dropArea: document.getElementById("dropArea"),
    fileInput: document.getElementById("fileElem"),
    preview: document.getElementById("preview")
  };

  // ======================
  // INITIALIZATION
  // ======================
  function initialize() {
    if (!dom.slidesContainer || !dom.newSlideBtn) {
      console.error("Critical elements missing!");
      return;
    }
    
    dom.slidesContainer.innerHTML = '';
    slides = [];
    createNewSlide();
    setupEventListeners();
    console.log("Quiz editor initialized successfully");
  }

  // ======================
  // CORE FUNCTIONS (MODIFIED)
  // ======================
  function createNewSlide(position = -1) {
    try {
      const slideId = Date.now();
      const slideHTML = `
        <div class="flex items-center gap-3 mb-4 slide" data-id="${slideId}" draggable="true">
          <span class="w-4 text-sm text-gray-700 text-right">${slides.length + 1}</span>
          <div class="relative flex w-[170px] h-[94px] bg-white items-center justify-center rounded-lg border border-[#D0D0D0]">
            <i class="ri-gallery-view-2"></i>
            <div class="absolute bottom-2 right-2">
              <button class="dropdown-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M6 12a2 2 0 114 0 2 2 0 01-4 0zm4 0a2 2 0 104 0 2 2 0 00-4 0zm4 0a2 2 0 104 0 2 2 0 00-4 0z"/>
                </svg>
              </button>
              <div class="hidden absolute right-0 bottom-8 w-40 bg-white border border-gray-200 rounded-md shadow-lg z-20 dropdown-menu">
                <ul class="py-1 text-sm text-gray-700">
                  <li><button class="w-full text-left px-4 py-2 hover:bg-gray-100 edit-slide-btn">✏️ Edit</button></li>
                  <li><button class="w-full text-left px-4 py-2 hover:bg-gray-100 duplicate-slide-btn">📄 Duplicate</button></li>
                  <li><button class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 delete-slide-btn">🗑️ Delete</button></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      `;

      if (position >= 0 && position < dom.slidesContainer.children.length) {
        dom.slidesContainer.children[position].insertAdjacentHTML('afterend', slideHTML);
      } else {
        dom.slidesContainer.insertAdjacentHTML('beforeend', slideHTML);
      }

      const slideData = {
        id: slideId,
        question: "Type your question here...",
        type: "multiple",
        options: ["Option 1", "Option 2", "Option 3", "Option 4"],
        correctAnswer: 0,
        image: null
      };

      if (position >= 0) {
        slides.splice(position, 0, slideData);
      } else {
        slides.push(slideData);
      }

      const newSlide = dom.slidesContainer.querySelector(`[data-id="${slideId}"]`);
      setupSlideEventListeners(newSlide, slideId);
      addDragHandlers(newSlide);
      renumberSlides();
      selectSlide(slideId);
      
      return slideId;
    } catch (error) {
      console.error("Error creating slide:", error);
      return null;
    }
  }

  // ======================
  // DRAG-AND-DROP FEATURES (NEW)
  // ======================
  function addDragHandlers(slideElement) {
    slideElement.addEventListener('dragstart', handleDragStart);
    slideElement.addEventListener('dragover', handleDragOver);
    slideElement.addEventListener('drop', handleDrop);
    slideElement.addEventListener('dragend', handleDragEnd);
  }

  function handleDragStart(e) {
    draggedSlide = e.target.closest('.slide');
    dragStartIndex = Array.from(dom.slidesContainer.children).indexOf(draggedSlide);
    e.dataTransfer.effectAllowed = 'move';
    draggedSlide.style.opacity = '0.5';
  }

  function handleDragOver(e) {
    e.preventDefault();
    const targetSlide = e.target.closest('.slide');
    if (!targetSlide || targetSlide === draggedSlide) return;
    
    const bounding = targetSlide.getBoundingClientRect();
    const offset = e.clientY - bounding.top;
    const insertPosition = offset < bounding.height / 2 ? 'beforebegin' : 'afterend';
    
    targetSlide.style.borderTop = insertPosition === 'beforebegin' ? '2px solid #A435F0' : 'none';
    targetSlide.style.borderBottom = insertPosition === 'afterend' ? '2px solid #A435F0' : 'none';
  }

  function handleDrop(e) {
    e.preventDefault();
    const targetSlide = e.target.closest('.slide');
    if (!targetSlide || !draggedSlide) return;

    const bounding = targetSlide.getBoundingClientRect();
    const insertPosition = e.clientY - bounding.top < bounding.height / 2 
      ? 'beforebegin' 
      : 'afterend';

    targetSlide.insertAdjacentElement(insertPosition, draggedSlide);
    const newIndex = Array.from(dom.slidesContainer.children).indexOf(draggedSlide);
    const movedSlide = slides.splice(dragStartIndex, 1)[0];
    slides.splice(newIndex, 0, movedSlide);

    renumberSlides();
    resetDragStyles();
  }

  function handleDragEnd() {
    resetDragStyles();
    draggedSlide = null;
    dragStartIndex = -1;
  }

  function resetDragStyles() {
    dom.slidesContainer.querySelectorAll('.slide').forEach(slide => {
      slide.style.opacity = '1';
      slide.style.borderTop = 'none';
      slide.style.borderBottom = 'none';
    });
  }

  // ======================
  // MODIFIED SLIDE ACTIONS
  // ======================
  function duplicateSlide(slideId) {
    const originalIndex = slides.findIndex(s => s.id === slideId);
    if (originalIndex === -1) return;

    const newId = createNewSlide(originalIndex + 1);
    if (!newId) return;

    const original = slides[originalIndex];
    const newSlide = slides[originalIndex + 1];
    
    newSlide.question = original.question + " (Copy)";
    newSlide.type = original.type;
    newSlide.options = JSON.parse(JSON.stringify(original.options));
    newSlide.correctAnswer = original.correctAnswer;
    newSlide.image = original.image;

    selectSlide(newId);
    updateMainContent();
  }

  function deleteSlide(slideId) {
    if (slides.length <= 1) {
      alert("Your quiz must have at least one slide!");
      return;
    }

    if (confirm('Are you sure you want to delete this slide?')) {
      document.querySelector(`.slide[data-id="${slideId}"]`)?.remove();
      slides = slides.filter(s => s.id !== slideId);
      renumberSlides();
      if (slides.length > 0) selectSlide(slides[0].id);
    }
  }

  // ======================
  // HELPER FUNCTIONS (NEW/UPDATED)
  // ======================
  function renumberSlides() {
    dom.slidesContainer.querySelectorAll('.slide').forEach((slideEl, index) => {
      slideEl.querySelector('span').textContent = index + 1;
    });
  }

  function selectSlide(slideId) {
    const slide = slides.find(s => s.id === slideId);
    if (!slide) return;
    
    currentSlide = slide;
    document.querySelectorAll('.slide').forEach(slideEl => {
      slideEl.classList.remove('border-purple-500', 'bg-purple-50');
    });
    
    const slideElement = document.querySelector(`.slide[data-id="${slideId}"]`);
    if (slideElement) {
      slideElement.classList.add('border-purple-500', 'bg-purple-50');
    }
    updateMainContent();
  }

  // ======================
  // EXISTING FUNCTIONALITY
  // ======================
  function setupSlideEventListeners(slideElement, slideId) {
    slideElement.addEventListener('click', (e) => {
      if (!e.target.closest('.dropdown-toggle') && !e.target.closest('.dropdown-menu')) {
        selectSlide(slideId);
      }
    });
    
    const dropdownToggle = slideElement.querySelector('.dropdown-toggle');
    dropdownToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleDropdown(e.currentTarget);
    });
    
    slideElement.querySelector('.edit-slide-btn')?.addEventListener('click', (e) => {
      e.stopPropagation();
      editSlide(slideId);
    });
    
    slideElement.querySelector('.duplicate-slide-btn')?.addEventListener('click', (e) => {
      e.stopPropagation();
      duplicateSlide(slideId);
    });
    
    slideElement.querySelector('.delete-slide-btn')?.addEventListener('click', (e) => {
      e.stopPropagation();
      deleteSlide(slideId);
    });
  }

  function updateMainContent() {
    if (!currentSlide) return;
    
    dom.questionText.textContent = currentSlide.question;
    dom.questionType.value = currentSlide.type;
    dom.contentArea.innerHTML = '';
    
    switch(currentSlide.type) {
      case 'multiple': renderMultipleChoice(); break;
      case 'fillblank': renderFillBlank(); break;
      case 'truefalse': renderTrueFalse(); break;
      case 'shortanswer': renderShortAnswer(); break;
    }
  }

  function renderMultipleChoice() {
    dom.contentArea.innerHTML = `
      <h2 class="text-xl font-small text-black mt-12 mb-2">Options</h2>
      <div class="grid grid-cols-2 gap-6 pt-4" id="optionsContainer">
        ${currentSlide.options.map((opt, i) => `
          <button class="bg-${getOptionColor(i)} text-black text-xl font-medium py-4 rounded-lg hover:bg-${getOptionHoverColor(i)} transition option-btn ${currentSlide.correctAnswer === i ? 'ring-2 ring-offset-2 ring-purple-500' : ''}"
                  data-index="${i}">
            ${opt}
          </button>
        `).join('')}
      </div>
      <div class="flex gap-2 mt-4">
        <button class="bg-gray-200 px-4 py-2 rounded-md hover:bg-gray-300" id="addOptionBtn">+ Add Option</button>
        <button class="bg-gray-200 px-4 py-2 rounded-md hover:bg-gray-300" id="removeOptionBtn" ${currentSlide.options.length <= 2 ? 'disabled' : ''}>- Remove Option</button>
      </div>
    `;
    
    document.querySelectorAll('.option-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const index = parseInt(e.currentTarget.dataset.index);
        currentSlide.correctAnswer = index;
        updateMainContent();
      });
    });
    
    document.getElementById("addOptionBtn")?.addEventListener('click', () => {
      currentSlide.options.push(`Option ${currentSlide.options.length + 1}`);
      updateMainContent();
    });
    
    document.getElementById("removeOptionBtn")?.addEventListener('click', () => {
      if (currentSlide.options.length > 2) {
        currentSlide.options.pop();
        if (currentSlide.correctAnswer >= currentSlide.options.length) {
          currentSlide.correctAnswer = currentSlide.options.length - 1;
        }
        updateMainContent();
      }
    });
  }

  function renderFillBlank() {
    dom.contentArea.innerHTML = `
      <h2 class="text-xl font-small text-black mt-12 mb-2">Answer</h2>
      <input type="text" class="w-full p-4 border border-gray-300 rounded-lg text-xl" 
             value="${currentSlide.options[0] || ''}" id="fillBlankAnswer">
    `;
    
    document.getElementById("fillBlankAnswer")?.addEventListener('input', (e) => {
      currentSlide.options[0] = e.target.value;
    });
  }

  function renderTrueFalse() {
    dom.contentArea.innerHTML = `
      <h2 class="text-xl font-small text-black mt-12 mb-2">Select Answer</h2>
      <div class="grid grid-cols-2 gap-6 pt-4">
        <button class="bg-green-500 text-black text-xl font-medium py-4 rounded-lg hover:bg-green-600 transition ${currentSlide.correctAnswer === 0 ? 'ring-2 ring-offset-2 ring-green-500' : ''}" id="trueBtn">
          True
        </button>
        <button class="bg-red-500 text-black text-xl font-medium py-4 rounded-lg hover:bg-red-600 transition ${currentSlide.correctAnswer === 1 ? 'ring-2 ring-offset-2 ring-red-500' : ''}" id="falseBtn">
          False
        </button>
      </div>
    `;
    
    document.getElementById("trueBtn")?.addEventListener('click', () => {
      currentSlide.correctAnswer = 0;
      updateMainContent();
    });
    
    document.getElementById("falseBtn")?.addEventListener('click', () => {
      currentSlide.correctAnswer = 1;
      updateMainContent();
    });
  }

  function renderShortAnswer() {
    dom.contentArea.innerHTML = `
      <h2 class="text-xl font-small text-black mt-12 mb-2">Expected Answer</h2>
      <textarea class="w-full p-4 border border-gray-300 rounded-lg text-xl h-32" 
                placeholder="Type the expected answer here..." 
                id="shortAnswerText">${currentSlide.options[0] || ''}</textarea>
    `;
    
    document.getElementById("shortAnswerText")?.addEventListener('input', (e) => {
      currentSlide.options[0] = e.target.value;
    });
  }

  function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    dropdown.classList.toggle('hidden');
    
    document.querySelectorAll('.dropdown-menu').forEach(item => {
      if (item !== dropdown && !item.classList.contains('hidden')) {
        item.classList.add('hidden');
      }
    });
    
    document.addEventListener('click', function handler(e) {
      if (!button.contains(e.target)) {
        dropdown.classList.add('hidden');
        document.removeEventListener('click', handler);
      }
    }, { once: true });
  }

  function getOptionColor(index) {
    const colors = ['red-500', 'yellow-400', 'sky-400', 'green-500', 'purple-500', 'pink-500'];
    return colors[index % colors.length];
  }

  function getOptionHoverColor(index) {
    const colors = ['red-600', 'yellow-500', 'sky-500', 'green-600', 'purple-600', 'pink-600'];
    return colors[index % colors.length];
  }

  function handleFileUpload() {
    const file = dom.fileInput.files[0];
    if (!file) return;

    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = () => {
        dom.preview.innerHTML = `<img src="${reader.result}" class="rounded-lg max-h-52 mx-auto" />`;
        if (currentSlide) {
          currentSlide.image = reader.result;
        }
      };
      reader.readAsDataURL(file);
    } else {
      dom.preview.innerHTML = `<p class="text-red-500 mt-2">Please upload a valid image file.</p>`;
    }
  }

  // ======================
  // EVENT LISTENERS
  // ======================
  function setupEventListeners() {
    dom.newSlideBtn?.addEventListener('click', () => createNewSlide());
    
    dom.questionType?.addEventListener('change', function() {
      if (currentSlide) {
        currentSlide.type = this.value;
        if (this.value === 'multiple') {
          currentSlide.options = ["Option 1", "Option 2", "Option 3", "Option 4"];
          currentSlide.correctAnswer = 0;
        } else if (this.value === 'truefalse') {
          currentSlide.options = ["True", "False"];
          currentSlide.correctAnswer = 0;
        } else {
          currentSlide.options = [""];
          currentSlide.correctAnswer = 0;
        }
        updateMainContent();
      }
    });

    dom.questionText?.addEventListener('input', function() {
      if (currentSlide) {
        currentSlide.question = this.textContent;
      }
    });

    // Image upload handlers
    dom.openModalBtn?.addEventListener('click', () => dom.imageModal.classList.remove('hidden'));
    dom.closeModalBtn?.addEventListener('click', () => dom.imageModal.classList.add('hidden'));
    dom.fileInput?.addEventListener('change', handleFileUpload);
    dom.dropArea?.addEventListener('click', () => dom.fileInput.click());
    dom.dropArea?.addEventListener('dragover', (e) => e.preventDefault());
    dom.dropArea?.addEventListener('drop', (e) => {
      e.preventDefault();
      dom.fileInput.files = e.dataTransfer.files;
      handleFileUpload();
    });

    // Collaboration modal handlers
    dom.openShareBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      dom.shareModal.classList.toggle('hidden');
      dom.collaboratorsModal.classList.add('hidden');
    });
    dom.cancelShareBtn?.addEventListener('click', () => dom.shareModal.classList.add('hidden'));
    dom.profileImage?.addEventListener('click', (e) => {
      e.stopPropagation();
      dom.collaboratorsModal.classList.toggle('hidden');
      dom.shareModal.classList.add('hidden');
    });
    dom.closeCollaboratorsBtn?.addEventListener('click', () => dom.collaboratorsModal.classList.add('hidden'));
    document.addEventListener('click', (e) => {
      if (!dom.shareContainer.contains(e.target)) {
        dom.shareModal.classList.add('hidden');
        dom.collaboratorsModal.classList.add('hidden');
      }
    });
  }

  // Start the application
  initialize();
});