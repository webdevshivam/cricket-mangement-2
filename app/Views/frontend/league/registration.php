
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MegaStar Pro Cricket League - League Registration</title>

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
        <h2 class="mb-0"><i class="fas fa-trophy me-2"></i>MPCL League Registration</h2>
      </div>

      <!-- Progress Bar -->
      <div class="progress-container">
        <div class="progress">
          <div class="progress-bar" role="progressbar" style="width: 33%"></div>
        </div>
        <div class="step-indicators">
          <div class="step-indicator active" data-step="1">
            <i class="fas fa-user"></i>
            <span>Basic Info</span>
          </div>
          <div class="step-indicator" data-step="2">
            <i class="fas fa-file-upload"></i>
            <span>Documents</span>
          </div>
          <div class="step-indicator" data-step="3">
            <i class="fas fa-credit-card"></i>
            <span>Payment</span>
          </div>
        </div>
      </div>

      <div class="card-body">
        <?php if (session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form id="leagueRegistrationForm" action="<?= base_url('league-registration-save') ?>" method="post" enctype="multipart/form-data" novalidate>
          <!-- Step 1: Basic Information -->
          <div class="step-content active" id="step1">
            <h4 class="step-title">
              <i class="fas fa-user me-2"></i>Basic Information
            </h4>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">
                  <i class="fas fa-user me-1"></i>Full Name *
                </label>
                <input name="name" type="text" class="form-control" id="name" value="<?= old('name') ?>" required>
                <div class="invalid-feedback">Please provide a valid name.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="age" class="form-label">
                  <i class="fas fa-calendar me-1"></i>Age *
                </label>
                <input name="age" type="number" class="form-control" id="age" min="8" max="100" value="<?= old('age') ?>" required>
                <div class="invalid-feedback">Please provide a valid age (8-100).</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label">
                  <i class="fas fa-phone me-1"></i>Mobile Number *
                </label>
                <input name="mobile" type="tel" class="form-control" id="mobile" pattern="[0-9]{10}" value="<?= old('mobile') ?>" required>
                <div class="invalid-feedback">Please provide a valid 10-digit mobile number.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">
                  <i class="fas fa-envelope me-1"></i>Email Address *
                </label>
                <input name="email" type="email" class="form-control" id="email" value="<?= old('email') ?>" required>
                <div class="invalid-feedback">Please provide a valid email address.</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="cricketer_type" class="form-label">
                  <i class="fas fa-baseball-ball me-1"></i>Cricketer Type *
                </label>
                <select name="cricketer_type" class="form-select" id="cricketer_type" onchange="showFees(this.value)" required>
                  <option value="">Select Cricketer Type</option>
                  <option value="bowler" <?= old('cricketer_type') == 'bowler' ? 'selected' : '' ?>>Bowler</option>
                  <option value="batsman" <?= old('cricketer_type') == 'batsman' ? 'selected' : '' ?>>Batsman</option>
                  <option value="wicket-keeper" <?= old('cricketer_type') == 'wicket-keeper' ? 'selected' : '' ?>>Wicket Keeper</option>
                  <option value="all-rounder" <?= old('cricketer_type') == 'all-rounder' ? 'selected' : '' ?>>All Rounder</option>
                </select>
                <div class="invalid-feedback">Please select a cricketer type.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="age_group" class="form-label">
                  <i class="fas fa-users me-1"></i>Age Group *
                </label>
                <select name="age_group" class="form-select" id="age_group" required>
                  <option value="">Select Age Group</option>
                  <option value="under_16" <?= old('age_group') == 'under_16' ? 'selected' : '' ?>>Under 16</option>
                  <option value="above_16" <?= old('age_group') == 'above_16' ? 'selected' : '' ?>>Above 16</option>
                </select>
                <div class="invalid-feedback">Please select an age group.</div>
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
                <input name="city" type="text" class="form-control" id="city" value="<?= old('city') ?>" required>
                <div class="invalid-feedback">Please provide your city name.</div>
              </div>
            </div>

            
          </div>

          <!-- Step 2: Document Upload -->
          <div class="step-content" id="step2">
            <h4 class="step-title">
              <i class="fas fa-file-upload me-2"></i>Document Upload
            </h4>

            <div class="alert alert-info mb-4">
              <h6><i class="fas fa-info-circle me-2"></i>Document Requirements</h6>
              <ul class="mb-0">
                <li><strong>Aadhar Card:</strong> PDF or Image (Max 5MB)</li>
                <li><strong>Marksheet:</strong> PDF only (Max 5MB)</li>
                <li><strong>Date of Birth Proof:</strong> PDF or Image (Max 5MB)</li>
                <li><strong>Photo:</strong> JPEG or PNG only (Max 2MB)</li>
              </ul>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="aadhar_document" class="form-label">
                  <i class="fas fa-id-card me-1"></i>Aadhar Card *
                </label>
                <input type="file" class="form-control" id="aadhar_document" name="aadhar_document" accept=".pdf,.jpg,.jpeg,.png" required>
                <div class="invalid-feedback">Please upload Aadhar document.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="marksheet_document" class="form-label">
                  <i class="fas fa-graduation-cap me-1"></i>Marksheet *
                </label>
                <input type="file" class="form-control" id="marksheet_document" name="marksheet_document" accept=".pdf" required>
                <div class="invalid-feedback">Please upload Marksheet (PDF only).</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="dob_proof" class="form-label">
                  <i class="fas fa-birthday-cake me-1"></i>Date of Birth Proof *
                </label>
                <input type="file" class="form-control" id="dob_proof" name="dob_proof" accept=".pdf,.jpg,.jpeg,.png" required>
                <div class="invalid-feedback">Please upload Date of Birth proof.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="photo" class="form-label">
                  <i class="fas fa-camera me-1"></i>Photo *
                </label>
                <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.jpeg,.png" required>
                <div class="invalid-feedback">Please upload your photo (JPEG/PNG only).</div>
              </div>
            </div>
          </div>

          <!-- Step 3: Payment -->
          <div class="step-content" id="step3">
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
        </form>
      </div>

      <!-- Navigation Buttons -->
      <div class="card-footer">
        <div class="d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;">
            <i class="fas fa-arrow-left me-2"></i>Previous
          </button>

          <div class="ms-auto">
            <button type="button" class="btn btn-golden" id="nextBtn">
              Next<i class="fas fa-arrow-right ms-2"></i>
            </button>
            <button type="submit" class="btn btn-golden" id="submitBtn" style="display: none;" form="leagueRegistrationForm">
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
          <p class="text-muted">Your league registration has been submitted successfully. You will receive a confirmation email shortly.</p>
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
  <script src="<?= base_url('assets-frontend/trial/') ?>league.js"></script>
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
