
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="container mt-3">
    <div class="card bg-dark border-warning">
        <div class="card-header">
            <h4 class="text-warning mb-0">
                <i class="fas fa-cogs me-2"></i>API & System Settings
            </h4>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- API Settings Form -->
            <form action="<?= base_url('admin/api-settings/update') ?>" method="post">
                
                <!-- OpenWeather API Settings -->
                <div class="card bg-secondary mb-4">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">
                            <i class="fas fa-cloud-sun me-2"></i>OpenWeather API Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="openweather_enabled" 
                                           name="openweather_enabled" value="1" 
                                           <?= $settings['openweather_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label text-light" for="openweather_enabled">
                                        Enable OpenWeather API
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="openweather_api_key" class="form-label text-light">API Key</label>
                                <input type="text" class="form-control bg-dark text-white" 
                                       id="openweather_api_key" name="openweather_api_key" 
                                       value="<?= esc($settings['openweather_api_key']) ?>"
                                       placeholder="Enter OpenWeather API Key">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="openweather_api_url" class="form-label text-light">API URL</label>
                                <input type="url" class="form-control bg-dark text-white" 
                                       id="openweather_api_url" name="openweather_api_url" 
                                       value="<?= esc($settings['openweather_api_url']) ?>"
                                       placeholder="https://api.openweathermap.org/data/2.5">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Razorpay API Settings -->
                <div class="card bg-secondary mb-4">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">
                            <i class="fas fa-credit-card me-2"></i>Razorpay Payment Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="razorpay_enabled" 
                                           name="razorpay_enabled" value="1" 
                                           <?= $settings['razorpay_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label text-light" for="razorpay_enabled">
                                        Enable Razorpay Payment Gateway
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="razorpay_key_id" class="form-label text-light">Razorpay Key ID</label>
                                <input type="text" class="form-control bg-dark text-white" 
                                       id="razorpay_key_id" name="razorpay_key_id" 
                                       value="<?= esc($settings['razorpay_key_id']) ?>"
                                       placeholder="rzp_test_xxxxxxxxxx">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="razorpay_key_secret" class="form-label text-light">Razorpay Key Secret</label>
                                <input type="password" class="form-control bg-dark text-white" 
                                       id="razorpay_key_secret" name="razorpay_key_secret" 
                                       value="<?= esc($settings['razorpay_key_secret']) ?>"
                                       placeholder="Enter Razorpay Key Secret">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SMS API Settings -->
                <div class="card bg-secondary mb-4">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">
                            <i class="fas fa-sms me-2"></i>SMS API Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms_enabled" 
                                           name="sms_enabled" value="1" 
                                           <?= $settings['sms_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label text-light" for="sms_enabled">
                                        Enable SMS API
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sms_api_key" class="form-label text-light">SMS API Key</label>
                                <input type="text" class="form-control bg-dark text-white" 
                                       id="sms_api_key" name="sms_api_key" 
                                       value="<?= esc($settings['sms_api_key']) ?>"
                                       placeholder="Enter SMS API Key">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sms_api_secret" class="form-label text-light">SMS API Secret</label>
                                <input type="password" class="form-control bg-dark text-white" 
                                       id="sms_api_secret" name="sms_api_secret" 
                                       value="<?= esc($settings['sms_api_secret']) ?>"
                                       placeholder="Enter SMS API Secret">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sms_api_url" class="form-label text-light">SMS API URL</label>
                                <input type="url" class="form-control bg-dark text-white" 
                                       id="sms_api_url" name="sms_api_url" 
                                       value="<?= esc($settings['sms_api_url']) ?>"
                                       placeholder="https://api.sms-provider.com/send">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Settings -->
                <div class="card bg-secondary mb-4">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">
                            <i class="fas fa-envelope me-2"></i>Email Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_enabled" 
                                           name="email_enabled" value="1" 
                                           <?= $settings['email_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label text-light" for="email_enabled">
                                        Enable Email Service
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_host" class="form-label text-light">SMTP Host</label>
                                <input type="text" class="form-control bg-dark text-white" 
                                       id="email_host" name="email_host" 
                                       value="<?= esc($settings['email_host']) ?>"
                                       placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="email_port" class="form-label text-light">SMTP Port</label>
                                <input type="number" class="form-control bg-dark text-white" 
                                       id="email_port" name="email_port" 
                                       value="<?= esc($settings['email_port']) ?>"
                                       placeholder="587">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="email_encryption" class="form-label text-light">Encryption</label>
                                <select class="form-control bg-dark text-white" id="email_encryption" name="email_encryption">
                                    <option value="tls" <?= $settings['email_encryption'] === 'tls' ? 'selected' : '' ?>>TLS</option>
                                    <option value="ssl" <?= $settings['email_encryption'] === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="" <?= empty($settings['email_encryption']) ? 'selected' : '' ?>>None</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_username" class="form-label text-light">Email Username</label>
                                <input type="email" class="form-control bg-dark text-white" 
                                       id="email_username" name="email_username" 
                                       value="<?= esc($settings['email_username']) ?>"
                                       placeholder="your-email@gmail.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_password" class="form-label text-light">Email Password</label>
                                <input type="password" class="form-control bg-dark text-white" 
                                       id="email_password" name="email_password" 
                                       value="<?= esc($settings['email_password']) ?>"
                                       placeholder="Enter email password or app password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_from_address" class="form-label text-light">From Email Address</label>
                                <input type="email" class="form-control bg-dark text-white" 
                                       id="email_from_address" name="email_from_address" 
                                       value="<?= esc($settings['email_from_address']) ?>"
                                       placeholder="noreply@cricketleague.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_from_name" class="form-label text-light">From Name</label>
                                <input type="text" class="form-control bg-dark text-white" 
                                       id="email_from_name" name="email_from_name" 
                                       value="<?= esc($settings['email_from_name']) ?>"
                                       placeholder="Cricket League">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Update API Settings
                    </button>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Section -->
    <div class="card bg-dark border-danger mt-4">
        <div class="card-header">
            <h5 class="text-danger mb-0">
                <i class="fas fa-key me-2"></i>Change Admin Password
            </h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/api-settings/change-password') ?>" method="post">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="current_password" class="form-label text-light">Current Password</label>
                        <input type="password" class="form-control bg-dark text-white" 
                               id="current_password" name="current_password" 
                               required placeholder="Enter current password">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="new_password" class="form-label text-light">New Password</label>
                        <input type="password" class="form-control bg-dark text-white" 
                               id="new_password" name="new_password" 
                               required minlength="6" placeholder="Enter new password">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="confirm_password" class="form-label text-light">Confirm New Password</label>
                        <input type="password" class="form-control bg-dark text-white" 
                               id="confirm_password" name="confirm_password" 
                               required minlength="6" placeholder="Confirm new password">
                    </div>
                </div>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-key me-2"></i>Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePassword() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords don't match");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    newPassword.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>

<?= $this->endSection(); ?>
