
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="container mt-3">
    <div class="card bg-dark border-warning">
        <div class="card-header">
            <h4 class="text-warning mb-0">
                <i class="fas fa-shield-alt me-2"></i>OTP Verification Settings
            </h4>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/otp-settings/update') ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-secondary mb-3">
                            <div class="card-header">
                                <h6 class="text-warning mb-0">Trial Registration OTP</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="trial_otp_enabled" 
                                           name="trial_otp_enabled" value="1" 
                                           <?= $settings['trial_otp_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label text-light" for="trial_otp_enabled">
                                        Enable OTP verification for trial registrations
                                    </label>
                                </div>
                                <small class="text-muted">
                                    When enabled, users will need to verify their email with OTP before completing trial registration.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-secondary mb-3">
                            <div class="card-header">
                                <h6 class="text-warning mb-0">League Registration OTP</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="league_otp_enabled" 
                                           name="league_otp_enabled" value="1" 
                                           <?= $settings['league_otp_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label text-light" for="league_otp_enabled">
                                        Enable OTP verification for league registrations
                                    </label>
                                </div>
                                <small class="text-muted">
                                    When enabled, users will need to verify their email with OTP before completing league registration.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-secondary">
                            <div class="card-header">
                                <h6 class="text-warning mb-0">OTP Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="otp_expiry_minutes" class="form-label text-light">OTP Expiry (Minutes)</label>
                                    <input type="number" class="form-control bg-dark text-white" 
                                           id="otp_expiry_minutes" name="otp_expiry_minutes" 
                                           value="<?= $settings['otp_expiry_minutes'] ?>" min="1" max="30">
                                    <small class="text-muted">OTP will expire after this many minutes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Update Settings
                    </button>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
