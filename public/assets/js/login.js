/**
 * Login Page JavaScript
 * Handles authentication, form validation, and login functionality
 */

// Initialize login page
document.addEventListener('DOMContentLoaded', function () {
  initializeLogin();
});

/**
 * Initialize login page functionality
 */
function initializeLogin() {
  initializeAnimations();
  console.log('Login page initialized');
}

/**
 * Initialize animations
 */
function initializeAnimations() {
  // Animate elements on page load
  const elements = document.querySelectorAll('.animate__animated');

  elements.forEach((element, index) => {
    element.style.animationDelay = `${index * 0.1}s`;
  });
}

/**
 * Toggle password visibility
 */
function togglePassword() {
  const passwordInput = document.getElementById('password');
  const passwordEye = document.getElementById('password-eye');

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    passwordEye.classList.remove('fa-eye');
    passwordEye.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    passwordEye.classList.remove('fa-eye-slash');
    passwordEye.classList.add('fa-eye');
  }
}

/**
 * Show loading spinner
 */
function showLoading() {
  document.getElementById('loading-spinner').classList.remove('d-none');
}

/**
 * Hide loading spinner
 */
function hideLoading() {
  document.getElementById('loading-spinner').classList.add('d-none');
}
