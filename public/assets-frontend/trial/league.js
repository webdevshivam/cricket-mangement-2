// League Registration Wizard
class LeagueWizard {
  constructor() {
    this.currentStep = 1;
    this.totalSteps = 2;
    this.form = document.getElementById('leagueRegistrationForm');
    this.nextBtn = document.getElementById('nextBtn');
    this.prevBtn = document.getElementById('prevBtn');
    this.submitBtn = document.getElementById('submitBtn');

    this.init();
  }

  init() {
    this.updateUI();
    this.bindEvents();
    this.loadStates();
  }

  bindEvents() {
    this.nextBtn.addEventListener('click', () => this.nextStep());
    this.prevBtn.addEventListener('click', () => this.prevStep());

    // Form validation on submit
    this.form.addEventListener('submit', (e) => this.handleSubmit(e));
  }

  nextStep() {
    if (this.validateCurrentStep()) {
      if (this.currentStep < this.totalSteps) {
        this.currentStep++;
        this.updateUI();
      }
    }
  }

  prevStep() {
    if (this.currentStep > 1) {
      this.currentStep--;
      this.updateUI();
    }
  }

  updateUI() {
    // Update progress bar
    const progressBar = document.querySelector('.progress-bar');
    const progressPercent = (this.currentStep / this.totalSteps) * 100;
    progressBar.style.width = `${progressPercent}%`;

    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
      indicator.classList.toggle('active', index + 1 <= this.currentStep);
    });

    // Show/hide step content
    document.querySelectorAll('.step-content').forEach((content, index) => {
      content.classList.toggle('active', index + 1 === this.currentStep);
    });

    // Update navigation buttons
    this.prevBtn.style.display = this.currentStep > 1 ? 'block' : 'none';

    if (this.currentStep === this.totalSteps) {
      this.nextBtn.style.display = 'none';
      this.submitBtn.style.display = 'block';
    } else {
      this.nextBtn.style.display = 'block';
      this.submitBtn.style.display = 'none';
    }
  }

  validateCurrentStep() {
    const currentStepDiv = document.getElementById(`step${this.currentStep}`);
    const requiredFields = currentStepDiv.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        field.classList.add('is-invalid');
        isValid = false;
      } else {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
      }
    });

    // Special validation for email
    const email = currentStepDiv.querySelector('input[type="email"]');
    if (email && email.value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email.value)) {
        email.classList.add('is-invalid');
        isValid = false;
      }
    }

    // Special validation for mobile
    const mobile = currentStepDiv.querySelector('input[type="tel"]');
    if (mobile && mobile.value) {
      const mobileRegex = /^[0-9]{10}$/;
      if (!mobileRegex.test(mobile.value)) {
        mobile.classList.add('is-invalid');
        isValid = false;
      }
    }

    return isValid;
  }

  handleSubmit(e) {
    if (!this.validateCurrentStep()) {
      e.preventDefault();
      return false;
    }
    return true;
  }

  loadStates() {
    const stateSelect = document.getElementById('state');
    const states = [
      "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh",
      "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka",
      "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram",
      "Nagaland", "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu",
      "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"
    ];

    states.forEach(state => {
      const option = document.createElement('option');
      option.value = state;
      option.textContent = state;
      stateSelect.appendChild(option);
    });
  }
}

// Show fees based on cricketer type
function showFees(cricketType) {
  const feesElement = document.getElementById('registration-fees');
  let fees = 0;

  switch(cricketType) {
    case 'bowler':
    case 'batsman':
      fees = 999;
      break;
    case 'wicket-keeper':
    case 'all-rounder':
      fees = 1199;
      break;
    default:
      fees = 0;
  }

  if (feesElement) {
    feesElement.textContent = `₹${fees}`;
  }
}

// Initialize the wizard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  new LeagueWizard();
});