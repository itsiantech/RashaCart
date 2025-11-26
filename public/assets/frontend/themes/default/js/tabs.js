// Product Tabs Functionality
document.addEventListener('livewire:navigated', function() {
  // Mobile Categories Toggle
  const mobileToggle = document.querySelector('.mobile-categories-toggle');
  const categoriesContainer = document.querySelector('.categories-container');
  const hamburgerBtn = document.querySelector('.hamburger-btn');
  
  if (mobileToggle && categoriesContainer && hamburgerBtn) {
    mobileToggle.addEventListener('click', function(e) {
      // Toggle active class on categories container
      categoriesContainer.classList.toggle('active');
      
      // Toggle aria-expanded attribute for accessibility
      const isExpanded = hamburgerBtn.getAttribute('aria-expanded') === 'true' || false;
      hamburgerBtn.setAttribute('aria-expanded', !isExpanded);
      
      // Prevent the click from propagating to document
      e.stopPropagation();
    });
    
    // Close categories when clicking outside
    document.addEventListener('click', function(e) {
      if (!categoriesContainer.contains(e.target) && !mobileToggle.contains(e.target)) {
        categoriesContainer.classList.remove('active');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
      }
    });
    
    // Close categories when pressing Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && categoriesContainer.classList.contains('active')) {
        categoriesContainer.classList.remove('active');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }
  
  // Get all tab buttons and tab content
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');
  
  // Add click event listeners to tab buttons
  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      const tabId = button.getAttribute('data-tab');
      
      // Remove active class from all buttons and contents
      tabButtons.forEach(btn => btn.classList.remove('active'));
      tabContents.forEach(content => content.classList.remove('active'));
      
      // Add active class to clicked button and corresponding content
      button.classList.add('active');
      const targetTab = document.getElementById(tabId);
      if (targetTab) targetTab.classList.add('active');
    });
  });
  
  // Initialize first tab as active if none is active
  if (!document.querySelector('.tab-btn.active') && tabButtons.length > 0) {
    tabButtons[0].classList.add('active');
    const firstTabId = tabButtons[0].getAttribute('data-tab');
    if (firstTabId) {
      document.getElementById(firstTabId)?.classList.add('active');
    }
  }
});
