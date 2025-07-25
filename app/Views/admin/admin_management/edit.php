
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Edit Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-light">
            <i class="fas fa-user-edit me-2"></i>Edit Admin
        </h1>
        <a href="/admin/manage-admins" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-dark border-secondary">
                <div class="card-header bg-secondary text-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-cog me-2"></i>Admin Details
                    </h5>
                </div>
                <div class="card-body">
                    <form action="/admin/manage-admins/update/<?= $admin['id'] ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label text-light">
                                    <i class="fas fa-user me-1"></i>Full Name *
                                </label>
                                <input type="text" 
                                       class="form-control bg-dark text-light border-secondary" 
                                       id="name" 
                                       name="name" 
                                       value="<?= old('name', $admin['name']) ?>" 
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label text-light">
                                    <i class="fas fa-envelope me-1"></i>Email Address *
                                </label>
                                <input type="email" 
                                       class="form-control bg-dark text-light border-secondary" 
                                       id="email" 
                                       name="email" 
                                       value="<?= old('email', $admin['email']) ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mobile" class="form-label text-light">
                                    <i class="fas fa-phone me-1"></i>Mobile Number *
                                </label>
                                <input type="tel" 
                                       class="form-control bg-dark text-light border-secondary" 
                                       id="mobile" 
                                       name="mobile" 
                                       value="<?= old('mobile', $admin['mobile']) ?>" 
                                       pattern="[0-9]{10}" 
                                       required>
                                <small class="text-muted">Enter 10 digit mobile number</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label text-light">
                                    <i class="fas fa-user-shield me-1"></i>Role
                                </label>
                                <input type="text" 
                                       class="form-control bg-secondary text-light border-secondary" 
                                       value="Admin" 
                                       readonly>
                            </div>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Password Change:</strong> Leave password fields empty if you don't want to change the password.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label text-light">
                                    <i class="fas fa-lock me-1"></i>New Password
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control bg-dark text-light border-secondary" 
                                           id="password" 
                                           name="password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimum 6 characters (leave empty to keep current password)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label text-light">
                                    <i class="fas fa-lock me-1"></i>Confirm New Password
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control bg-dark text-light border-secondary" 
                                           id="confirm_password" 
                                           name="confirm_password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye" id="confirm_password-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="/admin/manage-admins" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eyeIcon = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

// Mobile number validation
document.getElementById('mobile').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
    if (e.target.value.length > 10) {
        e.target.value = e.target.value.slice(0, 10);
    }
});
</script>
<?= $this->endSection() ?>
