<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MegaStar Pro Cricket League</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets-frontend/trial/') ?>trial.css">
</head>

<body>
  <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card form-wizard-card">
      <div class="card-header text-center">
        <div class="logo-container mb-3">
          <img src="https://megastarpremiercricketleague.com/registration/mccl/images/logo.png" alt="Cricket Logo" class="logo">
        </div>
        <h2 class="mb-0"><i class="fas fa-cricket me-2"></i>MPCL Trial Registration</h2>
      </div>

      <!-- Progress Bar -->
      <div class="progress-container">
        <div class="progress">
          <div class="progress-bar" role="progressbar" style="width: 50%"></div>
        </div>
        <div class="step-indicators">
          <div class="step-indicator active" data-step="1">
            <i class="fas fa-user"></i>
            <span>Basic Info</span>
          </div>
          <div class="step-indicator" data-step="2">
            <i class="fas fa-credit-card"></i>
            <span>Payment</span>
          </div>
        </div>
      </div>

      <div class="card-body">
        <!-- Step 1: Basic Information -->
        <div class="step-content active" id="step1">
          <h4 class="step-title">
            <i class="fas fa-user me-2"></i>Basic Information
          </h4>

          <form id="basicInfoForm" action="<?= base_url('trial-registration-save') ?>" method="post" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">
                  <i class="fas fa-user me-1"></i>Full Name *
                </label>
                <input name="name" type="text" class="form-control" id="name" required>
                <div class="invalid-feedback">Please provide a valid name.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="age" class="form-label">
                  <i class="fas fa-calendar me-1"></i>Age *
                </label>
                <input name="age" type="number" class="form-control" id="age" min="8" max="100" required>
                <div class="invalid-feedback">Please provide a valid age (18-100).</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label">
                  <i class="fas fa-phone me-1"></i>Mobile Number *
                </label>
                <input name="phone" type="tel" class="form-control" id="mobile" pattern="[0-9]{10}" required>
                <div class="invalid-feedback">Please provide a valid 10-digit mobile number.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">
                  <i class="fas fa-envelope me-1"></i>Email Address *
                </label>
                <input name="email" type="email" class="form-control" id="email" required>
                <div class="invalid-feedback">Please provide a valid email address.</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="state" class="form-label">
                  <i class="fas fa-map-marker-alt me-1"></i>State *
                </label>
                <select name="state" class="form-select" id="state" required>
                  <option value="">Select State</option>
                </select>
                <div class="invalid-feedback">Please select a state.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="city" class="form-label">
                  <i class="fas fa-building me-1"></i>City *
                </label>
                <input name="city" type="text" class="form-control" id="city" required>
                <div class="invalid-feedback">Please provide your city name.</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <label for="trialCity" class="form-label">
                  <i class="fas fa-star me-1"></i>Choose Trial City *
                </label>
                <select name="trialCity" class="form-select" id="trialCity" required>
                  <option value="">Select Trial City</option>
                  <?php foreach ($trial_cities as $city): ?>
                    <option value="<?= $city['id'] ?>"><?= $city['city_name'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a trial city.</div>
              </div>

              <div class="col-md-6 mb-4">
                <label for="cricketType" class="form-label">
                  <i class="fas fa-baseball-ball me-1"></i>Cricket Type *
                </label>
                <select name="cricket_type" class="form-select" id="cricketType" onchange="showFees(this.value)" required>
                  <option value="">Select Cricket Type</option>
                  <option value="bowler">Bowler</option>
                  <option value="batsman">Batsman</option>
                  <option value="wicket-keeper">Wicket Keeper</option>
                  <option value="all-rounder">All Rounder</option>
                </select>
                <div class="invalid-feedback">Please select a cricket type.</div>
              </div>
            </div>
          </form>
        </div>

        <!-- Step 2: Payment -->
        <div class="step-content" id="step2">
          <h4 class="step-title">
            <i class="fas fa-credit-card me-2"></i>Process to Payment
          </h4>

          <div class="text-center">
            <div class="payment-info mb-4">
              <h5 class="text-golden">Complete Your Payment</h5>
              <p class="text-muted">Scan the QR code below to proceed with payment</p>
            </div>

            <div class="qr-container">
              <div class="qr-code">
                <!-- QR Code SVG -->
                <img src="<?= base_url('uploads/qr_codes/' . $qr_code_setting['qr_code']) ?>" alt="QR Code" class="img-fluid">
              </div>
              <p class="qr-instructions mt-3">
                <i class="fas fa-mobile-alt me-2"></i>
                Use any UPI app to scan and pay
              </p>
            </div>

            <div class="payment-amount">
              <h3 class="text-golden" id="registration-fees">₹0</h3>
              <p class="text-muted">Registration Fee</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="card-footer">
        <div class="d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;">
            <i class="fas fa-arrow-left me-2"></i>Previous
          </button>

          <div class="ms-auto">
            <button type="button" class="btn btn-golden" id="nextBtn">
              Process to Payment<i class="fas fa-arrow-right ms-2"></i>
            </button>
            <button type="submit" class="btn btn-golden" id="submitBtn" style="display: none;">
              <i class="fas fa-check me-2"></i>Submit Registration
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title text-golden">
            <i class="fas fa-check-circle me-2"></i>Registration Successful!
          </h5>
        </div>
        <div class="modal-body text-center">
          <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
          <h4 class="mt-3">Thank You!</h4>
          <p class="text-muted">Your registration has been submitted successfully. You will receive a confirmation email shortly.</p>
        </div>
        <div class="modal-footer border-0 justify-content-center">
          <button type="button" class="btn btn-golden" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JS -->
  <script src="<?= base_url('assets-frontend/trial/') ?>trial.js"></script>
  <?php if (session()->get('success')): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
      });
    </script>
  <?php endif; ?>

</body>

</html>
