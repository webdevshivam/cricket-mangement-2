// Form Wizard JavaScript
class FormWizard {
  constructor() {
    this.currentStep = 1;
    this.totalSteps = 2;
    this.init();
  }

  init() {
    this.loadIndianStates();
    this.bindEvents();
    this.updateUI();
  }

  // Load Indian States (static data)
  loadIndianStates() {
    const states = [
      'Andhra Pradesh',
      'Arunachal Pradesh',
      'Assam',
      'Bihar',
      'Chhattisgarh',
      'Goa',
      'Gujarat',
      'Haryana',
      'Himachal Pradesh',
      'Jharkhand',
      'Karnataka',
      'Kerala',
      'Madhya Pradesh',
      'Maharashtra',
      'Manipur',
      'Meghalaya',
      'Mizoram',
      'Nagaland',
      'Odisha',
      'Punjab',
      'Rajasthan',
      'Sikkim',
      'Tamil Nadu',
      'Telangana',
      'Tripura',
      'Uttar Pradesh',
      'Uttarakhand',
      'West Bengal',
      'Andaman and Nicobar Islands',
      'Chandigarh',
      'Dadra and Nagar Haveli and Daman and Diu',
      'Delhi',
      'Jammu and Kashmir',
      'Ladakh',
      'Lakshadweep',
      'Puducherry',
    ];

    const stateSelect = document.getElementById('state');
    states.forEach((state) => {
      const option = document.createElement('option');
      option.value = state.toLowerCase().replace(/\s+/g, '_');
      option.textContent = state;
      stateSelect.appendChild(option);
    });
  }

  // Bind event listeners
  bindEvents() {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    nextBtn.addEventListener('click', () => this.nextStep());
    prevBtn.addEventListener('click', () => this.prevStep());
    submitBtn.addEventListener('click', () => this.submitForm());

    // Add real-time validation
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach((input) => {
      input.addEventListener('blur', () => this.validateField(input));
      input.addEventListener('input', () => this.clearValidation(input));
    });

    // Form submission prevention
    const forms = document.querySelectorAll('form');
    forms.forEach((form) => {
      form.addEventListener('submit', (e) => e.preventDefault());
    });
  }

  // Validate individual field
  validateField(field) {
    const isValid = field.checkValidity();

    if (isValid) {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
    } else {
      field.classList.remove('is-valid');
      field.classList.add('is-invalid');
    }

    return isValid;
  }

  // Clear validation styles
  clearValidation(field) {
    field.classList.remove('is-valid', 'is-invalid');
  }

  // Validate current step
  validateCurrentStep() {
    if (this.currentStep === 1) {
      return this.validateStep1();
    }
    return true;
  }

  // Validate Step 1
  validateStep1() {
    const form = document.getElementById('basicInfoForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach((input) => {
      if (!this.validateField(input)) {
        isValid = false;
      }
    });

    // Additional validation for mobile number
    const mobile = document.getElementById('mobile');
    const mobilePattern = /^[0-9]{10}$/;
    if (!mobilePattern.test(mobile.value)) {
      mobile.classList.add('is-invalid');
      isValid = false;
    }

    // Additional validation for email
    const email = document.getElementById('email');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value)) {
      email.classList.add('is-invalid');
      isValid = false;
    }

    // Additional validation for age
    const age = document.getElementById('age');
    const ageValue = parseInt(age.value);
    if (ageValue < 8 || ageValue > 100) {
      age.classList.add('is-invalid');
      isValid = false;
    }

    // Additional validation for cricket type
    const cricketType = document.getElementById('cricketType');
    if (!cricketType.value) {
      cricketType.classList.add('is-invalid');
      isValid = false;
    }

    if (!isValid) {
      this.showAlert('Please fill all required fields correctly.', 'error');
    }

    return isValid;
  }

  // Move to next step
  nextStep() {
    if (!this.validateCurrentStep()) {
      return;
    }

    if (this.currentStep < this.totalSteps) {
      this.currentStep++;
      this.updateUI();
      this.updateProgress();
      this.animateStepTransition();
    }
  }

  // Move to previous step
  prevStep() {
    if (this.currentStep > 1) {
      this.currentStep--;
      this.updateUI();
      this.updateProgress();
      this.animateStepTransition();
    }
  }

  // Update UI elements
  updateUI() {
    // Update step content visibility
    document.querySelectorAll('.step-content').forEach((step, index) => {
      step.classList.toggle('active', index + 1 === this.currentStep);
    });

    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
      indicator.classList.remove('active', 'completed');

      if (index + 1 === this.currentStep) {
        indicator.classList.add('active');
      } else if (index + 1 < this.currentStep) {
        indicator.classList.add('completed');
      }
    });

    // Update navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    prevBtn.style.display = this.currentStep > 1 ? 'block' : 'none';

    if (this.currentStep === this.totalSteps) {
      nextBtn.style.display = 'none';
      submitBtn.style.display = 'block';
    } else {
      nextBtn.style.display = 'block';
      submitBtn.style.display = 'none';
    }
  }

  // Update progress bar
  updateProgress() {
    const progressBar = document.querySelector('.progress-bar');
    const progress = (this.currentStep / this.totalSteps) * 100;
    progressBar.style.width = progress + '%';
  }

  // Animate step transition
  animateStepTransition() {
    const activeStep = document.querySelector('.step-content.active');
    activeStep.style.opacity = '0';
    activeStep.style.transform = 'translateX(20px)';

    setTimeout(() => {
      activeStep.style.opacity = '1';
      activeStep.style.transform = 'translateX(0)';
    }, 150);
  }

  // Submit form
  submitForm() {
    this.showLoadingState();

    // Simulate form submission delay
    const form = document.getElementById('basicInfoForm');
    form.submit();
  }

  // Show loading state
  showLoadingState() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML =
      '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    submitBtn.disabled = true;

    document.querySelector('.form-wizard-card').classList.add('loading');
  }

  // Hide loading state
  hideLoadingState() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML =
      '<i class="fas fa-check me-2"></i>Submit Registration';
    submitBtn.disabled = false;

    document.querySelector('.form-wizard-card').classList.remove('loading');
  }

  // Show success modal
  showSuccessModal() {
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();

    // Reset form after modal is hidden
    document
      .getElementById('successModal')
      .addEventListener('hidden.bs.modal', () => {
        this.resetForm();
      });
  }

  // Reset form
  resetForm() {
    this.currentStep = 1;
    this.updateUI();
    this.updateProgress();

    // Clear all form data
    document.getElementById('basicInfoForm').reset();

    // Clear validation classes
    document
      .querySelectorAll('.form-control, .form-select')
      .forEach((field) => {
        field.classList.remove('is-valid', 'is-invalid');
      });
  }

  // Show alert message
  showAlert(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${
      type === 'error' ? 'danger' : type
    } alert-dismissible fade show`;
    alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

    // Insert at the top of the form
    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alertDiv, cardBody.firstChild);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.remove();
      }
    }, 5000);
  }

  // Get form data
  getFormData() {
    return {
      name: document.getElementById('name').value,
      age: document.getElementById('age').value,
      mobile: document.getElementById('mobile').value,
      email: document.getElementById('email').value,
      state: document.getElementById('state').value,
      city: document.getElementById('city').value,
      trialCity: document.getElementById('trialCity').value,
      cricketType: document.getElementById('cricketType').value,
    };
  }
}

// Additional utility functions
class FormUtils {
  static formatPhoneNumber(phone) {
    // Format phone number as XXX-XXX-XXXX
    return phone.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
  }

  static capitalizeWords(str) {
    return str.replace(
      /\w\S*/g,
      (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase(),
    );
  }

  static validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  static validatePhone(phone) {
    const re = /^[0-9]{10}$/;
    return re.test(phone);
  }
}

// Enhanced input formatting
document.addEventListener('DOMContentLoaded', function () {
  // Initialize the form wizard
  window.formWizard = new FormWizard();

  // Add input formatting
  const nameInput = document.getElementById('name');
  const cityInput = document.getElementById('city');

  nameInput.addEventListener('input', function () {
    this.value = FormUtils.capitalizeWords(this.value);
  });

  cityInput.addEventListener('input', function () {
    this.value = FormUtils.capitalizeWords(this.value);
  });

  // Mobile number formatting and validation
  const mobileInput = document.getElementById('mobile');
  mobileInput.addEventListener('input', function () {
    // Remove non-numeric characters
    this.value = this.value.replace(/\D/g, '');

    // Limit to 10 digits
    if (this.value.length > 10) {
      this.value = this.value.slice(0, 10);
    }
  });

  // Add smooth scroll behavior
  document.documentElement.style.scrollBehavior = 'smooth';

  // Add keyboard navigation
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.target.matches('textarea')) {
      e.preventDefault();
      const nextBtn = document.getElementById('nextBtn');
      const submitBtn = document.getElementById('submitBtn');

      if (nextBtn.style.display !== 'none') {
        nextBtn.click();
      } else if (submitBtn.style.display !== 'none') {
        submitBtn.click();
      }
    }
  });

  // Add focus management
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('step-indicator')) {
      const step = parseInt(e.target.dataset.step);
      if (step < window.formWizard.currentStep) {
        window.formWizard.currentStep = step;
        window.formWizard.updateUI();
        window.formWizard.updateProgress();
      }
    }
  });
});

// Service Worker for offline functionality (optional)
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    // Service worker registration would go here
    console.log('Form Wizard loaded successfully');
  });
}

// Error handling
window.addEventListener('error', function (e) {
  console.error('An error occurred:', e.error);
});

// Performance monitoring
window.addEventListener('load', function () {
  const loadTime = performance.now();
  console.log(`Page loaded in ${loadTime.toFixed(2)}ms`);
});

function showFees(cricketType) {
  const feesElement = document.getElementById('registration-fees');
  let fees = 0;

  switch (cricketType) {
    case 'bowler':
      fees = 999;
      break;
    case 'batsman':
      fees = 999;
      break;
    case 'all-rounder':
      fees = 1199;
      break;
    case 'wicket-keeper':
      fees = 1199;
      break;
    default:
      fees = 0;
  }

  feesElement.textContent = `₹${fees}`;
}
