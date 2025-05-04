// quiz-editor.js
document.addEventListener("DOMContentLoaded", () => {
  // ======================
  // STATE MANAGEMENT
  // ======================
  let currentSlide = null;
  let slides = [];
  let draggedSlide = null;
  let dragStartIndex = -1;
  let quizId = null;
  let isNewQuiz = false;

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
    preview: document.getElementById("preview"),
    optionsContainer: document.getElementById("optionsContainer"),
    saveBtn: document.getElementById("saveBtn"),
    shareEmail: document.getElementById("shareEmail"),
    permissionSelect: document.getElementById("permissionSelect"),
    confirmShare: document.getElementById("confirmShare")
  };

  // ======================
  // INITIALIZATION
  // ======================
  async function initialize() {
    if (!dom.slidesContainer || !dom.newSlideBtn) {
      console.error("Critical elements missing!");
      return;
    }

    // Get quiz ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const quizParam = urlParams.get('quiz');
    
    if (quizParam && !isNaN(quizParam)) {
      // Existing quiz - load from server
      quizId = parseInt(quizParam);
      isNewQuiz = false;
      await loadQuizFromServer();
    } else {
      // New quiz - create default
      quizId = null;
      isNewQuiz = true;
      dom.slidesContainer.innerHTML = '';
      slides = [];
      createNewSlide();
    }

    setupEventListeners();
    console.log("Quiz editor initialized successfully");
  }

  // ======================
  // BACKEND INTEGRATION
  // ======================
  async function loadQuizFromServer() {
    try {
      const response = await fetch('../backend/quizBackend.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=load_quiz&quiz_id=${quizId}`
      });
  
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
  
      const data = await response.json();
      
      console.log('Received quiz data:', data); // Debug log
      
      if (data.status === 'success') {
        // Validate received data
        if (!data.quiz || !data.slides) {
          throw new Error('Invalid quiz data structure received');
        }
  
        // Clear existing slides
        dom.slidesContainer.innerHTML = '';
        slides = [];
        
        // Process slides from server
        if (Array.isArray(data.slides)) {
          data.slides.forEach((slide, index) => {
            const slideData = {
              id: slide.slide_id,
              question: slide.question || 'Untitled question',
              type: slide.question_type || 'multiple',
              options: slide.options || [],
              correctAnswer: slide.correctAnswer || 0,
              image: slide.image_url || null
            };
            
            slides.push(slideData);
            renderSlideThumbnail(slideData, index);
          });
  
          if (slides.length > 0) {
            selectSlide(slides[0].id);
          } else {
            createNewSlide();
          }
        } else {
          createNewSlide(); // Create default slide if no slides received
        }
      } else {
        throw new Error(data.message || 'Failed to load quiz');
      }
    } catch (error) {
      console.error('Error loading quiz:', error);
      showErrorMessage(`Failed to load quiz: ${error.message}`);
      
      // Fallback: Create a new empty quiz
      dom.slidesContainer.innerHTML = '';
      slides = [];
      createNewSlide();
    }
  }

  async function saveQuiz() {
    try {
      // If this is a new quiz, create it first
      if (isNewQuiz) {
        const title = document.title || 'Untitled Quiz';
        const response = await fetch('../backend/quizBackend.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `action=create_quiz&title=${encodeURIComponent(title)}`
        });

        const data = await response.json();
        
        if (data.status === 'success') {
          quizId = data.quiz_id;
          isNewQuiz = false;
          // Update URL to include the new quiz ID
          window.history.replaceState(null, '', `?quiz=${quizId}`);
        } else {
          throw new Error(data.message || 'Failed to create quiz');
        }
      }

      // Save slides
      const saveResponse = await fetch('../backend/quizBackend.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=save_slides&quiz_id=${quizId}&slides=${encodeURIComponent(JSON.stringify(slides))}`
      });

      const saveData = await saveResponse.json();
      
      if (saveData.status === 'success') {
        showSuccessMessage('Quiz saved successfully!');
      } else {
        throw new Error(saveData.message || 'Failed to save slides');
      }
    } catch (error) {
      console.error('Error saving quiz:', error);
      showErrorMessage('Failed to save quiz: ' + error.message);
    }
  }

  async function addCollaborator(email, permission) {
    try {
      if (!quizId) {
        throw new Error('Quiz must be saved before adding collaborators');
      }

      const response = await fetch('../backend/quizBackend.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_collaborator&quiz_id=${quizId}&email=${encodeURIComponent(email)}&permission=${permission}`
      });

      const data = await response.json();
      
      if (data.status === 'success') {
        showSuccessMessage('Collaborator added successfully!');
        dom.shareModal.classList.add('hidden');
        dom.shareEmail.value = '';
      } else {
        throw new Error(data.message || 'Failed to add collaborator');
      }
    } catch (error) {
      console.error('Error adding collaborator:', error);
      showErrorMessage('Failed to add collaborator: ' + error.message);
    }
  }

  // ======================
  // UI HELPERS
  // ======================
  function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.remove();
    }, 3000);
  }

  function showErrorMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.remove();
    }, 3000);
  }

  // ======================
  // CORE FUNCTIONS
  // ======================
  function createNewSlide(position = -1) {
    try {
      const slideId = Date.now();
      
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

      renderSlideThumbnail(slideData, position >= 0 ? position : slides.length - 1);
      selectSlide(slideId);

      return slideId;
    } catch (error) {
      console.error("Error creating slide:", error);
      return null;
    }
  }

  function renderSlideThumbnail(slideData, position) {
    const slideHTML = `
      <div class="flex items-center gap-3 mb-4 slide" data-id="${slideData.id}" draggable="true">
        <span class="w-4 text-sm text-gray-700 text-right">${position + 1}</span>
        <div class="relative flex w-[170px] h-[94px] bg-white items-center justify-center rounded-lg border border-[#D0D0D0]">
          ${slideData.image ? 
            `<img src="${slideData.image}" class="w-full h-full object-cover rounded-lg" />` : 
            `<i class="ri-gallery-view-2"></i>`}
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

    const newSlide = dom.slidesContainer.querySelector(`[data-id="${slideData.id}"]`);
    setupSlideEventListeners(newSlide, slideData.id);
    addDragHandlers(newSlide);
    renumberSlides();
  }

  // ======================
  // DRAG-AND-DROP FUNCTIONS
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
  // SLIDE ACTIONS
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
  // HELPER FUNCTIONS
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

  // ======================
  // CONTENT RENDERING
  // ======================
  function updateMainContent() {
    if (!currentSlide) return;

    dom.questionText.textContent = currentSlide.question;
    dom.questionType.value = currentSlide.type;
    dom.contentArea.innerHTML = '';

    switch (currentSlide.type) {
      case 'multiple':
        renderMultipleChoice();
        renderOptionsEditor();
        break;
      case 'fillblank':
        renderFillBlank();
        renderAnswerEditor();
        break;
      case 'shortanswer':
        renderShortAnswer();
        renderAnswerEditor();
        break;
    }
  }

  function renderOptionsEditor() {
    dom.optionsContainer.innerHTML = `
      <h3 class="text-sm font-medium mb-2">Options</h3>
      <div class="space-y-2">
        ${currentSlide.options.map((option, index) => `
          <div class="flex items-center gap-2">
            <input type="radio" name="correctOption" 
                   ${currentSlide.correctAnswer === index ? 'checked' : ''} 
                   value="${index}" class="correct-option-radio">
            <input type="text" value="${option}" 
                   class="w-full p-1 border rounded option-input" 
                   data-index="${index}">
            <button class="delete-option-btn text-red-500" data-index="${index}">×</button>
          </div>
        `).join('')}
      </div>
      <button id="addOptionBtn" class="mt-2 text-sm text-purple-700 hover:underline">+ Add Option</button>
    `;

    document.querySelectorAll('.option-input').forEach(input => {
      input.addEventListener('input', (e) => {
        const index = parseInt(e.target.dataset.index);
        currentSlide.options[index] = e.target.value;
        const optionText = document.querySelector(`.option-btn[data-index="${index}"] .option-text`);
        if (optionText) {
          optionText.textContent = e.target.value;
        }
      });
    });

    document.querySelectorAll('.correct-option-radio').forEach(radio => {
      radio.addEventListener('change', (e) => {
        currentSlide.correctAnswer = parseInt(e.target.value);
        updateMainContent();
      });
    });

    document.querySelectorAll('.delete-option-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const index = parseInt(e.target.dataset.index);
        if (currentSlide.options.length <= 2) {
          alert("You must have at least 2 options");
          return;
        }
        currentSlide.options.splice(index, 1);
        if (currentSlide.correctAnswer >= index) {
          currentSlide.correctAnswer = Math.max(0, currentSlide.correctAnswer - 1);
        }
        updateMainContent();
      });
    });

    document.getElementById('addOptionBtn')?.addEventListener('click', () => {
      if (currentSlide.options.length < 6) {
        currentSlide.options.push(`Option ${currentSlide.options.length + 1}`);
        updateMainContent();
      }
    });
  }

  function renderAnswerEditor() {
    dom.optionsContainer.innerHTML = `
      <h3 class="text-sm font-medium mb-2">Correct Answer</h3>
      <input type="text" value="${currentSlide.options[0] || ''}" 
             class="w-full p-2 border rounded answer-input">
    `;

    document.querySelector('.answer-input')?.addEventListener('input', (e) => {
      currentSlide.options[0] = e.target.value;
    });
  }

  function renderMultipleChoice() {
    dom.contentArea.innerHTML = `
      <h2 class="text-xl font-small text-black mt-12 mb-2">Options</h2>
      <div class="grid grid-cols-2 gap-6 pt-4" style="grid-auto-rows: minmax(0, 1fr); align-items: stretch;">
        ${currentSlide.options.map((opt, i) => `
          <div class="relative h-full">
            <button class="w-full h-full bg-${getOptionColor(i)} text-black text-xl font-medium py-4 rounded-lg hover:bg-${getOptionHoverColor(i)} transition-all option-btn ${currentSlide.correctAnswer === i ? 'ring-2 ring-offset-2 ring-purple-500' : ''} min-h-[60px] whitespace-normal break-words flex items-center justify-center px-2"
                    data-index="${i}">
              <span class="option-text" contenteditable="true">${opt}</span>
            </button>
            ${currentSlide.correctAnswer === i ?
        '<div class="absolute top-0 right-0 bg-purple-500 text-white text-xs px-2 py-1 rounded-bl-lg rounded-tr-lg">Correct</div>' :
        ''}
          </div>
        `).join('')}
      </div>
    `;

    document.querySelectorAll('.option-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        if (e.target.classList.contains('option-text')) return;
        const index = parseInt(e.currentTarget.dataset.index);
        currentSlide.correctAnswer = index;
        updateMainContent();
      });
    });

    document.querySelectorAll('.option-text').forEach((span, index) => {
      span.addEventListener('input', (e) => {
        currentSlide.options[index] = e.target.textContent;
      });

      span.addEventListener('blur', (e) => {
        currentSlide.options[index] = e.target.textContent;
      });
    });
  }

  function renderFillBlank() {
    dom.contentArea.innerHTML = `
      <h2 class="text-xl font-small text-black mt-12 mb-2">Answer</h2>
      <div class="bg-gray-100 p-4 rounded-lg">
        <p class="text-gray-700">Participants will see a blank field to fill in</p>
      </div>
    `;
  }

  function renderShortAnswer() {
    dom.contentArea.innerHTML = `
      <h2 class="text-xl font-small text-black mt-12 mb-2">Expected Answer</h2>
      <div class="bg-gray-100 p-4 rounded-lg">
        <p class="text-gray-700">Participants will see a text area to type their answer</p>
      </div>
    `;
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
  // IMAGE UPLOAD
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
          // Update thumbnail preview
          const slideElement = document.querySelector(`.slide[data-id="${currentSlide.id}"]`);
          if (slideElement) {
            const imgContainer = slideElement.querySelector('.relative');
            imgContainer.innerHTML = `
              <img src="${reader.result}" class="w-full h-full object-cover rounded-lg" />
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
            `;
            setupSlideEventListeners(slideElement, currentSlide.id);
          }
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

    dom.questionType?.addEventListener('change', function () {
      if (currentSlide) {
        currentSlide.type = this.value;
        if (this.value === 'multiple') {
          currentSlide.options = ["Option 1", "Option 2", "Option 3", "Option 4"];
          currentSlide.correctAnswer = 0;
        } else {
          currentSlide.options = [""];
          currentSlide.correctAnswer = 0;
        }
        updateMainContent();
      }
    });

    dom.questionText?.addEventListener('input', function () {
      if (currentSlide) {
        currentSlide.question = this.textContent;
      }
    });

    // Image upload
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

    // Save button
    dom.saveBtn?.addEventListener('click', () => saveQuiz());

    // Collaboration modals
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

    // Share collaborator
    dom.confirmShare?.addEventListener('click', () => {
      const email = dom.shareEmail.value.trim();
      const permission = dom.permissionSelect.value;
      
      if (!email) {
        showErrorMessage('Please enter an email address');
        return;
      }
      
      addCollaborator(email, permission);
    });
  }

  // Start the application
  initialize();
});