
<?= $this->extend('layouts/frontend') ?>

<?= $this->section('title') ?>Check Trial Status<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-search me-2"></i>
                            Check Trial Status
                        </h3>
                        <p class="mb-0 mt-2">Enter your mobile number to check your trial status and grade</p>
                    </div>
                    
                    <div class="card-body p-5">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= base_url('trial-status-check') ?>">
                            <?= csrf_field() ?>
                            
                            <div class="form-group mb-4">
                                <label for="mobile" class="form-label fw-bold">
                                    <i class="fas fa-mobile-alt me-2"></i>
                                    Mobile Number
                                </label>
                                <input type="tel" 
                                       class="form-control form-control-lg" 
                                       id="mobile" 
                                       name="mobile" 
                                       placeholder="Enter your 10-digit mobile number"
                                       pattern="[0-9]{10}"
                                       maxlength="10"
                                       required>
                                <div class="form-text">
                                    Enter the mobile number you used during trial registration
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-2"></i>
                                    Check Status
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-2">Haven't registered yet?</p>
                            <a href="<?= base_url('trial-registration') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                Register for Trial
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-white">
                        <i class="fas fa-shield-alt me-2"></i>
                        Your information is secure and confidential
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('mobile').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
<?= $this->endSection() ?>
