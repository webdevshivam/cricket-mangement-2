<?php
$request = \Config\Services::request();
$session = \Config\Services::session();
$lang = $request->getGet('lang') ?? 'en';
$session->set('language', $lang);

// Load the language file for the selected language
$languageService = \Config\Services::language();
$languageService->setLocale($lang);
helper('language');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

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
        <h2 class="mb-0"><i class="fas fa-cricket me-2"></i><?= lang('Frontend.trial_registration') ?></h2>

        <!-- Language Selector -->
        <div class="language-selector mt-3">
          <select class="form-select form-select-sm" onchange="changeLanguage(this.value)" style="width: auto; display: inline-block;">
            <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
            <option value="hi" <?= $lang === 'hi' ? 'selected' : '' ?>>हिंदी</option>
            <option value="te" <?= $lang === 'te' ? 'selected' : '' ?>>తెలుగు</option>
            <option value="ta" <?= $lang === 'ta' ? 'selected' : '' ?>>தமிழ்</option>
          </select>
        </div>
      </div>

      <!-- Progress Bar -->
      <div class="progress-container">
        <div class="progress">
          <div class="progress-bar" role="progressbar" style="width: 50%"></div>
        </div>
        <div class="step-indicators">
          <div class="step-indicator active" data-step="1">
            <i class="fas fa-user"></i>
            <span><?= lang('Frontend.basic_information') ?></span>
          </div>
          <div class="step-indicator" data-step="2">
            <i class="fas fa-credit-card"></i>
            <span><?= lang('Frontend.payment_information') ?></span>
          </div>
        </div>
      </div>

      <div class="card-body">
        <!-- Step 1: Basic Information -->
        <div class="step-content active" id="step1">
          <h4 class="step-title">
            <i class="fas fa-user me-2"></i><?= lang('Frontend.basic_information') ?>
          </h4>

          <form id="basicInfoForm" action="<?= base_url('trial-registration-save') ?>" method="post" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">
                  <i class="fas fa-user me-1"></i><?= lang('Frontend.full_name') ?> *
                </label>
                <input name="name" type="text" class="form-control" id="name" required>
                <div class="invalid-feedback"><?= lang('Frontend.provide_valid_name') ?></div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="age" class="form-label">
                  <i class="fas fa-calendar me-1"></i><?= lang('Frontend.age') ?> *
                </label>
                <input name="age" type="number" class="form-control" id="age" min="8" max="100" required>
                <div class="invalid-feedback"><?= lang('Frontend.provide_valid_age') ?></div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label">
                  <i class="fas fa-phone me-1"></i><?= lang('Frontend.mobile') ?> *
                </label>
                <input name="phone" type="tel" class="form-control" id="mobile" pattern="[0-9]{10}" required>
                <div class="invalid-feedback"><?= lang('Frontend.provide_valid_mobile') ?></div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">
                  <i class="fas fa-envelope me-1"></i><?= lang('Frontend.email') ?> *
                </label>
                <input name="email" type="email" class="form-control" id="email" required>
                <div class="invalid-feedback"><?= lang('Frontend.provide_valid_email') ?></div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="state" class="form-label">
                  <i class="fas fa-map-marker-alt me-1"></i><?= lang('Frontend.state') ?> *
                </label>
                <select name="state" class="form-select" id="state" required>
                  <option value=""><?= lang('Frontend.select_state') ?></option>
                </select>
                <div class="invalid-feedback"><?= lang('Frontend.select_state') ?></div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="city" class="form-label">
                  <i class="fas fa-building me-1"></i><?= lang('Frontend.city') ?> *
                </label>
                <input name="city" type="text" class="form-control" id="city" required>
                <div class="invalid-feedback"><?= lang('Frontend.provide_city_name') ?></div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <label for="trialCity" class="form-label">
                  <i class="fas fa-star me-1"></i><?= lang('Frontend.trial_city') ?> *
                </label>
                <select name="trialCity" class="form-select" id="trialCity" required>
                  <option value=""><?= lang('Frontend.select_trial_city') ?></option>
                  <?php foreach ($trial_cities as $city): ?>
                    <option value="<?= $city['id'] ?>"><?= $city['city_name'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback"><?= lang('Frontend.select_trial_city') ?></div>
              </div>

              <div class="col-md-6 mb-4">
                <label for="cricketType" class="form-label">
                  <i class="fas fa-baseball-ball me-1"></i><?= lang('Frontend.cricket_type') ?> *
                </label>
                <select name="cricket_type" class="form-select" id="cricketType" onchange="showFees(this.value)" required>
                  <option value=""><?= lang('Frontend.select_cricket_type') ?></option>
                  <option value="bowler"><?= lang('Frontend.bowler') ?></option>
                  <option value="batsman"><?= lang('Frontend.batsman') ?></option>
                  <option value="wicket-keeper"><?= lang('Frontend.wicket_keeper') ?></option>
                  <option value="all-rounder"><?= lang('Frontend.all_rounder') ?></option>
                </select>
                <div class="invalid-feedback"><?= lang('Frontend.select_cricket_type') ?></div>
              </div>
            </div>
          </form>
        </div>

        <!-- Step 2: Payment -->
        <div class="step-content" id="step2">
          <h4 class="step-title">
            <i class="fas fa-credit-card me-2"></i><?= lang('Frontend.process_to_payment') ?>
          </h4>

          <div class="text-center">
            <div class="payment-info mb-4">
              <h5 class="text-golden"><?= lang('Frontend.complete_payment') ?></h5>
              <p class="text-muted"><?= lang('Frontend.scan_qr_code') ?></p>
            </div>

            <div class="qr-container">
              <div class="qr-code">
                <!-- QR Code SVG -->
                <img src="<?= base_url('uploads/qr_codes/' . $qr_code_setting['qr_code']) ?>" alt="QR Code" class="img-fluid">
              </div>
              <p class="qr-instructions mt-3">
                <i class="fas fa-mobile-alt me-2"></i>
                <?= lang('Frontend.use_upi_app') ?>
              </p>
            </div>

            <div class="payment-amount">
              <h3 class="text-golden" id="registration-fees">₹0</h3>
              <p class="text-muted"><?= lang('Frontend.registration_fee') ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="card-footer">
        <div class="d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;">
            <i class="fas fa-arrow-left me-2"></i><?= lang('Frontend.previous') ?>
          </button>

          <div class="ms-auto">
            <button type="button" class="btn btn-golden" id="nextBtn">
              <?= lang('Frontend.process_to_payment') ?><i class="fas fa-arrow-right ms-2"></i>
            </button>
            <button type="submit" class="btn btn-golden" id="submitBtn" style="display: none;">
              <i class="fas fa-check me-2"></i><?= lang('Frontend.submit_registration') ?>
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
            <i class="fas fa-check-circle me-2"></i><?= lang('Frontend.registration_successful') ?>
          </h5>
        </div>
        <div class="modal-body text-center">
          <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
          <h4 class="mt-3"><?= lang('Frontend.thank_you') ?></h4>
          <p class="text-muted"><?= lang('Frontend.registration_success_msg') ?></p>
        </div>
        <div class="modal-footer border-0 justify-content-center">
          <button type="button" class="btn btn-golden" data-bs-dismiss="modal"><?= lang('Frontend.close') ?></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JS -->
  <script src="<?= base_url('assets-frontend/trial/') ?>trial.js"></script>

  <script>
  function changeLanguage(lang) {
    const url = new URL(window.location);
    url.searchParams.set('lang', lang);
    window.location = url;
  }
  </script>

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