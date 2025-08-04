/**
 * Main JavaScript file for Cricket League Management Dashboard
 * Handles global functionality, navigation, and common utilities
 */

// Global variables
let currentPage = 'dashboard';
let sidebar = null;
let content = null;

// Initialize application when DOM is ready
$(document).ready(function () {
  initializeApp();
});

/**
 * Initialize the application
 */
function initializeApp() {
  // Cache DOM elements
  sidebar = $('#sidebar');
  content = $('#content');

  // Initialize components
  initializeSidebar();
  initializeTooltips();
  initializeSelect2();
  initializeModals();
  initializeCharts();

  // Load dashboard by default
  loadPage('dashboard');

  // Initialize event listeners
  initializeEventListeners();

  console.log('Cricket League Management Dashboard initialized successfully');
}

/**
 * Initialize sidebar functionality
 */
function initializeSidebar() {
  // Sidebar toggle functionality
  $('#sidebarCollapse').on('click', function () {
    sidebar.toggleClass('active');
    content.toggleClass('active');
  });

  // Navigation click handlers
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]'),
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

/**
 * Initialize Select2 dropdowns
 */
function initializeSelect2() {
  $('.select2').select2({
    theme: 'default',
    width: '100%',
    placeholder: 'Select an option...',
    allowClear: true,
  });
}

/**
 * Initialize modal functionality
 */
function initializeModals() {
  // Initialize modal functionality
  $('.modal').on('show.bs.modal', function () {
    // Reset forms when modal opens
    $(this).find('form')[0]?.reset();
    $(this).find('.select2').val(null).trigger('change');

    // Ensure proper z-index
    var zIndex = 1055 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
      $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
  });

  // Fix modal backdrop issues
  $('.modal').on('hidden.bs.modal', function () {
    $('.modal-backdrop').remove();
    if ($('.modal.show').length > 0) {
      $('body').addClass('modal-open');
    }
  });

  // Prevent modal from closing when clicking inside modal content
  $('.modal').on('click', function(e) {
    if (e.target === this) {
      $(this).modal('hide');
    }
  });

  // Handle form submissions in modals
  $('.modal form').on('submit', function(e) {
    var submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
  });
}

/**
 * Initialize Charts.js defaults
 */
function initializeCharts() {
  Chart.defaults.color = '#ffffff';
  Chart.defaults.borderColor = '#444444';
  Chart.defaults.backgroundColor = 'rgba(255, 215, 0, 0.1)';
}

/**
 * Initialize global event listeners
 */
function initializeEventListeners() {
  // Global form submission handler

  // Global button click handler for common actions
  $('body').on('click', '[data-action]', function () {
    const action = $(this).data('action');
    const target = $(this).data('target');
    handleAction(action, target, $(this));
  });

  // Global search functionality
  $('body').on('input', '.global-search', function () {
    const searchTerm = $(this).val();
    performGlobalSearch(searchTerm);
  });

  // Window resize handler
  $(window).on('resize', function () {
    handleWindowResize();
  });
}

/**
 * Load a specific page
 * @param {string} pageName - Name of the page to load
 */
function loadPage(pageName) {
  currentPage = pageName;

  // Hide all page contents
  $('.page-content').removeClass('active');

  // Show loading spinner
  showLoading();

  if (pageName === 'dashboard') {
    // Dashboard is already in the main HTML
    $('#dashboard-content').addClass('active');
    initializeDashboard();
    hideLoading();
  } else {
    // Load dynamic content
    loadDynamicPage(pageName);
  }

  // Update page title
  updatePageTitle(pageName);
}

/**
 * Load dynamic page content
 * @param {string} pageName - Name of the page to load
 */
function loadDynamicPage(pageName) {
  const pageFile = `pages/${pageName}.html`;

  $.ajax({
    url: pageFile,
    type: 'GET',
    success: function (data) {
      $('#dynamic-content').html(data);
      $('#dynamic-content .page-content').addClass('active');

      // Initialize page-specific functionality
      initializePageSpecific(pageName);
      hideLoading();
    },
    error: function () {
      showError(`Failed to load ${pageName} page`);
      hideLoading();
    },
  });
}

/**
 * Initialize page-specific functionality
 * @param {string} pageName - Name of the page
 */
function initializePageSpecific(pageName) {
  switch (pageName) {
    case 'players':
      if (typeof initializePlayers === 'function') {
        loadScript('assets/js/players.js', initializePlayers);
      }
      break;
    case 'teams':
      if (typeof initializeTeams === 'function') {
        loadScript('assets/js/teams.js', initializeTeams);
      }
      break;
    case 'matches':
      if (typeof initializeMatches === 'function') {
        loadScript('assets/js/matches.js', initializeMatches);
      }
      break;
    case 'tournaments':
      if (typeof initializeTournaments === 'function') {
        loadScript('assets/js/tournaments.js', initializeTournaments);
      }
      break;
    case 'statistics':
      if (typeof initializeStatistics === 'function') {
        loadScript('assets/js/statistics.js', initializeStatistics);
      }
      break;
    case 'components':
      if (typeof initializeComponents === 'function') {
        loadScript('assets/js/components.js', initializeComponents);
      }
      break;
  }

  // Re-initialize common components for new content
  initializeTooltips();
  initializeSelect2();
}

/**
 * Load JavaScript file dynamically
 * @param {string} src - Source path of the JavaScript file
 * @param {function} callback - Callback function to execute after loading
 */
function loadScript(src, callback) {
  if ($(`script[src="${src}"]`).length === 0) {
    const script = document.createElement('script');
    script.src = src;
    script.onload = callback;
    document.head.appendChild(script);
  } else {
    callback();
  }
}

/**
 * Handle form submissions
 * @param {jQuery} form - jQuery form object
 */

/**
 * Handle common actions
 * @param {string} action - Action to perform
 * @param {string} target - Target element or ID
 * @param {jQuery} element - jQuery element that triggered the action
 */

/**
 * Confirm delete action
 * @param {string} target - Target ID to delete
 * @param {jQuery} element - jQuery element
 */
function confirmDelete(target, element) {
  if (confirm('Are you sure you want to delete this item? This action cannot be undone!')) {
    performDelete(target, element);
  }
}

/**
 * Perform delete operation
 * @param {string} target - Target ID to delete
 * @param {jQuery} element - jQuery element
 */
function performDelete(target, element) {
  showLoading();

  // Simulate API call
  setTimeout(() => {
    // Remove element from UI
    element.closest('tr').fadeOut(300, function () {
      $(this).remove();
    });

    showSuccess('Item deleted successfully!');
    hideLoading();
  }, 1000);
}

/**
 * Show loading spinner
 */
function showLoading() {
  $('#loading-spinner').fadeIn(300);
}

/**
 * Hide loading spinner
 */
function hideLoading() {
  $('#loading-spinner').fadeOut(300);
}

/**
 * Show success message
 * @param {string} message - Success message to show
 */
function showSuccess(message) {
  if (typeof notyf !== 'undefined') {
    notyf.success(message);
  } else {
    alert('Success: ' + message);
  }
}

/**
 * Show error message
 * @param {string} message - Error message to show
 */
function showError(message) {
  if (typeof notyf !== 'undefined') {
    notyf.error(message);
  } else {
    alert('Error: ' + message);
  }
}

/**
 * Show celebration animation
 */
function showCelebration() {
  const celebration = $('#celebration');
  celebration.fadeIn(300);

  setTimeout(() => {
    celebration.fadeOut(1000);
  }, 2000);
}

/**
 * Update page title
 * @param {string} pageName - Name of the current page
 */
function updatePageTitle(pageName) {
  const titles = {
    dashboard: 'Dashboard Overview',
    players: 'Player Management',
    teams: 'Team Management',
    matches: 'Match Management',
    tournaments: 'Tournament Management',
    statistics: 'Statistics & Analytics',
    settings: 'Settings',
  };

  document.title = `${titles[pageName]} - Cricket League Management`;
}

/**
 * Refresh current page data
 */
function refreshCurrentPageData() {
  if (currentPage === 'dashboard') {
    initializeDashboard();
  } else {
    // Refresh data tables or other dynamic content
    const tables = $('.data-table').DataTable();
    if (tables) {
      tables.ajax.reload();
    }
  }
}

/**
 * Refresh specific data
 * @param {string} target - Target to refresh
 */
function refreshData(target) {
  showLoading();

  // Simulate data refresh
  setTimeout(() => {
    if (target) {
      $(`#${target}`).trigger('refresh');
    } else {
      refreshCurrentPageData();
    }

    showSuccess('Data refreshed successfully!');
    hideLoading();
  }, 1000);
}

/**
 * Export data functionality
 * @param {string} format - Export format (csv, excel, pdf)
 */
function exportData(format) {
  showLoading();

  // Simulate export process
  setTimeout(() => {
    showSuccess(`Data exported as ${format.toUpperCase()} successfully!`);
    hideLoading();
  }, 2000);
}

/**
 * Perform global search
 * @param {string} searchTerm - Search term
 */
function performGlobalSearch(searchTerm) {
  if (searchTerm.length < 3) return;

  // Filter tables or content based on search term
  $('.data-table').DataTable().search(searchTerm).draw();
}

/**
 * Handle window resize
 */
function handleWindowResize() {
  // Responsive chart resizing
  Chart.helpers.each(Chart.instances, function (instance) {
    instance.resize();
  });
}

/**
 * Initialize form wizard
 * @param {string} wizardId - ID of the wizard container
 */
function initializeFormWizard(wizardId) {
  const wizard = $(`#${wizardId}`);
  let currentStep = 0;
  const steps = wizard.find('.wizard-step');
  const totalSteps = steps.length;

  // Show first step
  showStep(0);

  // Next button handler
  wizard.on('click', '.btn-next', function () {
    if (validateStep(currentStep)) {
      if (currentStep < totalSteps - 1) {
        currentStep++;
        showStep(currentStep);
      }
    }
  });

  // Previous button handler
  wizard.on('click', '.btn-prev', function () {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });

  // Step indicator click handler
  wizard.on('click', '.step', function () {
    const stepIndex = $(this).index();
    if (stepIndex <= currentStep || validateAllPreviousSteps(stepIndex)) {
      currentStep = stepIndex;
      showStep(currentStep);
    }
  });

  function showStep(stepIndex) {
    steps.removeClass('active');
    steps.eq(stepIndex).addClass('active');

    // Update step indicators
    wizard.find('.step').each(function (index) {
      if (index < stepIndex) {
        $(this).addClass('completed').removeClass('active');
      } else if (index === stepIndex) {
        $(this).addClass('active').removeClass('completed');
      } else {
        $(this).removeClass('active completed');
      }
    });

    // Update navigation buttons
    wizard.find('.btn-prev').toggle(stepIndex > 0);
    wizard.find('.btn-next').toggle(stepIndex < totalSteps - 1);
    wizard.find('.btn-submit').toggle(stepIndex === totalSteps - 1);
  }

  function validateStep(stepIndex) {
    const step = steps.eq(stepIndex);
    const inputs = step.find('input[required], select[required]');
    let isValid = true;

    inputs.each(function () {
      if (!this.value.trim()) {
        $(this).addClass('is-invalid');
        isValid = false;
      } else {
        $(this).removeClass('is-invalid');
      }
    });

    return isValid;
  }

  function validateAllPreviousSteps(targetStep) {
    for (let i = 0; i < targetStep; i++) {
      if (!validateStep(i)) {
        return false;
      }
    }
    return true;
  }
}

/**
 * Format number with commas
 * @param {number} num - Number to format
 * @returns {string} Formatted number
 */
function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Format date
 * @param {Date|string} date - Date to format
 * @returns {string} Formatted date
 */
function formatDate(date) {
  return moment(date).format('MMM DD, YYYY');
}

/**
 * Format time
 * @param {Date|string} time - Time to format
 * @returns {string} Formatted time
 */
function formatTime(time) {
  return moment(time).format('HH:mm');
}

/**
 * Generate unique ID
 * @returns {string} Unique ID
 */
function generateId() {
  return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

/**
 * Debounce function
 * @param {function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @param {boolean} immediate - Execute immediately
 * @returns {function} Debounced function
 */
function debounce(func, wait, immediate) {
  let timeout;
  return function executedFunction() {
    const context = this;
    const args = arguments;
    const later = function () {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}

/**
 * Logout function
 */
function logout() {
  // Clear stored user data
  localStorage.removeItem('cricketAdmin');
  sessionStorage.removeItem('cricketAdmin');

  // Show logout confirmation
  if (typeof notyf !== 'undefined') {
    notyf.success('You have been successfully logged out.');
  }

  // Redirect to login page after a short delay
  setTimeout(() => {
    window.location.href = 'login.html';
  }, 1000);
}

/**
 * Check authentication status
 */
function checkAuthStatus() {
  const user =
    localStorage.getItem('cricketAdmin') ||
    sessionStorage.getItem('cricketAdmin');

  if (!user) {
    // User is not logged in, redirect to login
    window.location.href = 'login.html';
    return false;
  }

  return true;
}

// Make logout function available globally
window.logout = logout;

// Export functions for use in other modules
window.CricketDashboard = {
  showLoading,
  hideLoading,
  showSuccess,
  showError,
  showCelebration,
  initializeFormWizard,
  formatNumber,
  formatDate,
  formatTime,
  generateId,
  debounce,
  logout,
  checkAuthStatus,
};

$(document).ready(function () {
  // Sidebar Toggle
  $('#sidebarCollapse').on('click', function () {
    $('#sidebar').toggleClass('hidden');
    $('#content').toggleClass('active');
  });

  // Auto-activate sidebar based on current URL
  function setActiveSidebarItem() {
    var currentPath = window.location.pathname;
    var currentUrl = window.location.href;

    // Remove active classes from all items
    $('.sidebar .nav-item').removeClass('active');
    $('.sidebar .nav-link').removeClass('active');
    $('.sidebar .collapse').removeClass('show');
    $('.sidebar .dropdown-toggle').attr('aria-expanded', 'false');

    // Find matching nav link
    $('.sidebar .nav-link').each(function() {
      var linkHref = $(this).attr('href');

      if (linkHref && (currentUrl === linkHref || currentPath === linkHref.replace(window.location.origin, ''))) {
        // Only add active class to the specific link
        $(this).addClass('active');

        // If this is inside a dropdown, expand the dropdown but don't add active class to parent
        var parentDropdown = $(this).closest('.collapse');
        if (parentDropdown.length) {
          parentDropdown.addClass('show');
          parentDropdown.siblings('.dropdown-toggle').attr('aria-expanded', 'true');
          // Don't add active class to parent nav-item for submenu links
        } else {
          // Only add active class to parent nav-item for top-level links
          $(this).closest('.nav-item').addClass('active');
        }

        return false; // Break the loop
      }
    });
  }

  // Set active item on page load
  setActiveSidebarItem();

  // Dropdown toggle functionality
  $('.sidebar .dropdown-toggle').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    var target = $(this).attr('href');
    var $collapse = $(target);
    var $parentLi = $(this).closest('.nav-item');
    var isExpanded = $(this).attr('aria-expanded') === 'true';

    // Close other dropdowns smoothly
    $('.sidebar .collapse').not($collapse).each(function() {
      $(this).slideUp(200, function() {
        $(this).removeClass('show');
      });
    });
    $('.sidebar .dropdown-toggle').not(this).attr('aria-expanded', 'false');

    // Toggle current dropdown with smooth animation
    if (isExpanded) {
      $collapse.slideUp(200, function() {
        $(this).removeClass('show');
      });
      $(this).attr('aria-expanded', 'false');
    } else {
      $collapse.addClass('show').hide().slideDown(200);
      $(this).attr('aria-expanded', 'true');
    }
  });

  // Close all dropdowns function
  function closeAllDropdowns() {
    $('.sidebar .collapse').removeClass('show');
    $('.sidebar .dropdown-toggle').attr('aria-expanded', 'false');
  }

  // Close dropdowns when clicking outside sidebar
  $(document).on('click', function(e) {
    if (!$(e.target).closest('.sidebar').length) {
      closeAllDropdowns();
    }
  });

  // Close dropdowns when pressing ESC key
  $(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
      closeAllDropdowns();
    }
  });

  // Optional: Add a button to manually close all dropdowns
  $(document).on('click', '.close-all-menus', function(e) {
    e.preventDefault();
    closeAllDropdowns();
  });

  // Handle nav link clicks
  $('.sidebar .nav-link').on('click', function(e) {
    var href = $(this).attr('href');

    // If it's not a dropdown toggle and has a proper href
    if (!$(this).hasClass('dropdown-toggle') && href && href !== '#' && !href.startsWith('#')) {
      // Let the browser handle the navigation normally
      return true;
    }
  });

  // Tooltip initialization
  $('[data-bs-toggle="tooltip"]').tooltip();

  // Page Navigation
  $(document).on('click', '[data-page]', function (e) {
    e.preventDefault();
    var page = $(this).data('page');
    loadPage(page);
  });

  function loadPage(page) {
    // Show loading spinner
    $('#loading-spinner').show();

    // Hide all page contents
    $('.page-content').removeClass('active');

    // Simulate loading delay
    setTimeout(function () {
      $('#loading-spinner').hide();

      // Show specific page content
      if ($('#' + page + '-content').length) {
        $('#' + page + '-content').addClass('active');
      } else {
        // Load dynamic content
        loadDynamicContent(page);
      }
    }, 500);
  }

  function loadDynamicContent(page) {
    var content = '';
    switch (page) {
      case 'add-player':
        content = getAddPlayerForm();
        break;
      case 'assign-grades':
        content = getAssignGradesForm();
        break;
      default:
        content = '<h2>Page Under Development</h2><p>This feature is coming soon.</p>';
    }

    $('#dynamic-content').html(content).addClass('active');
  }

  // Initialize Select2
  if ($.fn.select2) {
    $('.select2').select2({
      theme: 'default'
    });
  }

  // Initialize DataTables
  if ($.fn.DataTable) {
    $('.data-table').DataTable({
      responsive: true,
      pageLength: 10,
      language: {
        search: 'Search:',
        lengthMenu: 'Show _MENU_ entries',
        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
        paginate: {
          first: 'First',
          last: 'Last',
          next: 'Next',
          previous: 'Previous'
        }
      }
    });
  }
});

// Logout function
function logout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = '<?= base_url() ?>/logout';
  }
}