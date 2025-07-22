
class LeagueWizard {
  constructor() {
    this.currentStep = 1;
    this.totalSteps = 3;
    this.initializeWizard();
    this.loadStates();
  }

  initializeWizard() {
    this.updateStepDisplay();
    this.setupEventListeners();
  }

  setupEventListeners() {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    nextBtn?.addEventListener('click', () => this.nextStep());
    prevBtn?.addEventListener('click', () => this.prevStep());

    // Form validation
    const form = document.getElementById('leagueRegistrationForm');
    form?.addEventListener('submit', (e) => this.handleSubmit(e));

    // State change handler
    const stateSelect = document.getElementById('state');
    stateSelect?.addEventListener('change', () => this.loadCities());
  }

  nextStep() {
    if (this.validateCurrentStep()) {
      if (this.currentStep < this.totalSteps) {
        this.currentStep++;
        this.updateStepDisplay();
      }
    }
  }

  prevStep() {
    if (this.currentStep > 1) {
      this.currentStep--;
      this.updateStepDisplay();
    }
  }

  updateStepDisplay() {
    // Hide all step contents
    document.querySelectorAll('.step-content').forEach(step => {
      step.classList.remove('active');
    });

    // Show current step
    const currentStepElement = document.getElementById(`step${this.currentStep}`);
    currentStepElement?.classList.add('active');

    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
      indicator.classList.toggle('active', index + 1 <= this.currentStep);
    });

    // Update progress bar
    const progressBar = document.querySelector('.progress-bar');
    const progressPercentage = (this.currentStep / this.totalSteps) * 100;
    progressBar.style.width = `${progressPercentage}%`;

    // Update buttons
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    prevBtn.style.display = this.currentStep > 1 ? 'inline-block' : 'none';
    
    if (this.currentStep === this.totalSteps) {
      nextBtn.style.display = 'none';
      submitBtn.style.display = 'inline-block';
    } else {
      nextBtn.style.display = 'inline-block';
      submitBtn.style.display = 'none';
    }

    // Update next button text
    if (this.currentStep === 1) {
      nextBtn.innerHTML = 'Next<i class="fas fa-arrow-right ms-2"></i>';
    } else if (this.currentStep === 2) {
      nextBtn.innerHTML = 'Process to Payment<i class="fas fa-arrow-right ms-2"></i>';
    }
  }

  validateCurrentStep() {
    const currentStepElement = document.getElementById(`step${this.currentStep}`);
    const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add('is-invalid');
        isValid = false;
      } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
      }

      // Special validation for mobile number
      if (input.type === 'tel' && input.value.length !== 10) {
        input.classList.add('is-invalid');
        isValid = false;
      }

      // File validation
      if (input.type === 'file' && this.currentStep === 2) {
        if (!input.files.length) {
          input.classList.add('is-invalid');
          isValid = false;
        } else {
          const file = input.files[0];
          const maxSize = input.id === 'photo' ? 2048 * 1024 : 5120 * 1024; // 2MB for photo, 5MB for others
          
          if (file.size > maxSize) {
            input.classList.add('is-invalid');
            alert(`File ${file.name} is too large. Maximum size allowed is ${maxSize / (1024 * 1024)}MB.`);
            isValid = false;
          } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
          }
        }
      }
    });

    return isValid;
  }

  handleSubmit(e) {
    if (!this.validateCurrentStep()) {
      e.preventDefault();
      return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    submitBtn.disabled = true;

    // Form will submit naturally if validation passes
    return true;
  }

  async loadStates() {
    try {
      const response = await fetch('https://api.countrystatecity.in/v1/countries/IN/states', {
        headers: {
          'X-CSCAPI-KEY': 'API_KEY_HERE' // Replace with actual API key or use static data
        }
      });
      
      if (!response.ok) {
        // Fallback to static state data
        this.loadStaticStates();
        return;
      }
      
      const states = await response.json();
      const stateSelect = document.getElementById('state');
      
      states.forEach(state => {
        const option = document.createElement('option');
        option.value = state.iso2;
        option.textContent = state.name;
        stateSelect.appendChild(option);
      });
    } catch (error) {
      console.error('Error loading states:', error);
      this.loadStaticStates();
    }
  }

  loadStaticStates() {
    const states = [
      'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
      'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
      'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
      'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
      'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
      'Uttar Pradesh', 'Uttarakhand', 'West Bengal'
    ];

    const stateSelect = document.getElementById('state');
    states.forEach(state => {
      const option = document.createElement('option');
      option.value = state;
      option.textContent = state;
      stateSelect.appendChild(option);
    });
  }

  // Get form data
  getFormData() {
    return {
      name: document.getElementById('name').value,
      age: document.getElementById('age').value,
      mobile: document.getElementById('mobile').value,
      email: document.getElementById('email').value,
      cricketer_type: document.getElementById('cricketer_type').value,
      age_group: document.getElementById('age_group').value,
      state: document.getElementById('state').value,
      city: document.getElementById('city').value,
      trial_city_id: document.getElementById('trial_city_id').value,
    };
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
