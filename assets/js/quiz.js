document.addEventListener("DOMContentLoaded", () => {
  // ======================
  // STATE MANAGEMENT
  // ======================
  let currentSlide = null;
  let slides = [];
  
  // ======================
  // DOM ELEMENTS
  // ======================
  const dom = {
    // Slide management
    newSlideBtn: document.getElementById("newSlideBtn"),
    slidesContainer: document.getElementById("slidesContainer"),
    
    // Editor elements
    questionType: document.getElementById("questionType"),
    mainContent: document.getElementById("mainContent"),
    contentArea: document.getElementById("contentArea"),
    questionText: document.getElementById("questionText"),
    
    // Modals
    shareModal: document.getElementById("shareModal"),
    collaboratorsModal: document.getElementById("collaboratorsModal"),
    openShareBtn: document.getElementById("openShareModal"),
    profileImage: document.getElementById("profileImage"),
    cancelShareBtn: document.getElementById("cancelShare"),
    closeCollaboratorsBtn: document.getElementById("closeCollaborators"),
    shareContainer: document.getElementById("shareContainer"),
    
    // Image upload
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
    // Verify critical elements exist
    if (!dom.slidesContainer || !dom.newSlideBtn) {
      console.error("Critical elements missing!");
      return;
    }
    
    // Clear any existing content
    dom.slidesContainer.innerHTML = '';
    slides = [];
    
    // Create first slide
    createNewSlide();
    
    // Set up event listeners
    setupEventListeners();
    
    console.log("Quiz editor initialized successfully");
  }

  // ======================
  // CORE FUNCTIONS
  // ======================
  function createNewSlide() {
    try {
      const slideId = Date.now();
      const slideNumber = slides.length + 1;
      
      const slideHTML = `
        <div class="flex items-center gap-3 mb-4 slide" data-id="${slideId}">
          <span class="w-4 text-sm text-gray-700 text-right">${slideNumber}</span>
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
      
      dom.slidesContainer.insertAdjacentHTML('beforeend', slideHTML);
      
      const slideData = {
        id: slideId,
        question: "Type your question here...",
        type: "multiple",
        options: ["Option 1", "Option 2", "Option 3", "Option 4"],
        correctAnswer: 0,
        image: null
      };
      
      slides.push(slideData);
      selectSlide(slideId);
      
      // Add event listeners to the new slide
      const newSlide = dom.slidesContainer.lastElementChild;
      setupSlideEventListeners(newSlide, slideId);
      
      return slideId;
    } catch (error) {
      console.error("Error creating slide:", error);
      return null;
    }
  }

  function setupSlideEventListeners(slideElement, slideId) {
    // Slide selection
    slideElement.addEventListener('click', (e) => {
      if (!e.target.closest('.dropdown-toggle') && !e.target.closest('.dropdown-menu')) {
        selectSlide(slideId);
      }
    });
    
    // Dropdown toggle
    const dropdownToggle = slideElement.querySelector('.dropdown-toggle');
    dropdownToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleDropdown(e.currentTarget);
    });
    
    // Slide actions
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

  function selectSlide(slideId) {
    const slide = slides.find(s => s.id === slideId);
    if (!slide) return;
    
    currentSlide = slide;
    
    // Update UI
    document.querySelectorAll('.slide').forEach(slideEl => {
      slideEl.classList.remove('border-purple-500', 'bg-purple-50');
    });
    
    const slideElement = document.querySelector(`.slide[data-id="${slideId}"]`);
    if (slideElement) {
      slideElement.classList.add('border-purple-500', 'bg-purple-50');
    }
    
    // Update editor
    updateMainContent();
  }

  function updateMainContent() {
    if (!currentSlide) return;
    
    dom.questionText.textContent = currentSlide.question;
    dom.questionType.value = currentSlide.type;
    dom.contentArea.innerHTML = '';
    
    switch(currentSlide.type) {
      case 'multiple':
        renderMultipleChoice();
        break;
      case 'fillblank':
        renderFillBlank();
        break;
      case 'truefalse':
        renderTrueFalse();
        break;
      case 'shortanswer':
        renderShortAnswer();
        break;
    }
  }

  // ======================
  // QUESTION TYPE RENDERERS
  // ======================

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
    
    // Add option event listeners
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

  // ======================
  // SLIDE ACTIONS
  // ======================

  function editSlide(slideId) {
    dom.questionText.focus();
    console.log("Editing slide:", slideId);
  }

  function duplicateSlide(slideId) {
    const original = slides.find(s => s.id === slideId);
    if (!original) return;
    
    const newId = createNewSlide();
    if (!newId) return;
    
    const newSlide = slides.find(s => s.id === newId);
    if (!newSlide) return;
    
    // Copy properties
    newSlide.question = original.question + " (Copy)";
    newSlide.type = original.type;
    newSlide.options = [...original.options];
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
      // Remove from DOM
      document.querySelector(`.slide[data-id="${slideId}"]`)?.remove();
      
      // Remove from array
      slides = slides.filter(s => s.id !== slideId);
      
      // Renumber slides
      document.querySelectorAll('.slide').forEach((el, i) => {
        el.querySelector('span').textContent = i + 1;
      });
      
      // Select another slide
      if (slides.length > 0) selectSlide(slides[0].id);
    }
  }

  // ======================
  // UTILITY FUNCTIONS
  // ======================

  function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    dropdown.classList.toggle('hidden');
    
    // Close other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(item => {
      if (item !== dropdown && !item.classList.contains('hidden')) {
        item.classList.add('hidden');
      }
    });
    
    // Close when clicking outside
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

  // ======================
  // EVENT HANDLERS
  // ======================

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
    // Slide management
    dom.newSlideBtn?.addEventListener('click', createNewSlide);
    
    // Question editor
    dom.questionType?.addEventListener('change', function() {
      if (currentSlide) {
        currentSlide.type = this.value;
        // Reset options based on type
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

    // Image upload
    dom.openModalBtn?.addEventListener('click', () => {
      dom.imageModal.classList.remove('hidden');
      dom.imageModal.classList.add('flex');
    });

    dom.closeModalBtn?.addEventListener('click', () => {
      dom.imageModal.classList.add('hidden');
    });

    dom.fileInput?.addEventListener('change', handleFileUpload);
    dom.dropArea?.addEventListener('click', () => dom.fileInput.click());

    dom.dropArea?.addEventListener('dragover', (e) => {
      e.preventDefault();
      dom.dropArea.classList.add('border-blue-500', 'bg-blue-50');
    });

    dom.dropArea?.addEventListener('dragleave', () => {
      dom.dropArea.classList.remove('border-blue-500', 'bg-blue-50');
    });

    dom.dropArea?.addEventListener('drop', (e) => {
      e.preventDefault();
      dom.dropArea.classList.remove('border-blue-500', 'bg-blue-50');
      if (e.dataTransfer.files.length) {
        dom.fileInput.files = e.dataTransfer.files;
        handleFileUpload();
      }
    });

    // Share modal
    dom.openShareBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      dom.shareModal.classList.toggle('hidden');
      dom.collaboratorsModal.classList.add('hidden');
    });

    dom.cancelShareBtn?.addEventListener('click', () => {
      dom.shareModal.classList.add('hidden');
    });

    dom.profileImage?.addEventListener('click', (e) => {
      e.stopPropagation();
      dom.collaboratorsModal.classList.toggle('hidden');
      dom.shareModal.classList.add('hidden');
    });

    dom.closeCollaboratorsBtn?.addEventListener('click', () => {
      dom.collaboratorsModal.classList.add('hidden');
    });

    // Close modals when clicking outside
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
