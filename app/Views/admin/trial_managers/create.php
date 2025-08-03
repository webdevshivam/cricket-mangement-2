
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Create Trial Manager<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-primary">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-plus text-primary"></i> Create Trial Manager
                    </h4>
                </div>

                <div class="card-body">
                    <?php if (session()->get('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->get('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="/admin/trial-managers" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Manager Name</label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           id="name" name="name" value="<?= old('name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control bg-dark text-white border-secondary" 
                                           id="email" name="email" value="<?= old('email') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trial_name" class="form-label">Trial Name</label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           id="trial_name" name="trial_name" value="<?= old('trial_name') ?>" 
                                           placeholder="e.g., Mumbai Cricket Trial 2024" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trial_city_id" class="form-label">Trial City (Optional)</label>
                                    <select class="form-select bg-dark text-white border-secondary" 
                                            id="trial_city_id" name="trial_city_id">
                                        <option value="">Select City</option>
                                        <?php foreach ($trial_cities as $city): ?>
                                            <option value="<?= $city['id'] ?>" 
                                                    <?= old('trial_city_id') == $city['id'] ? 'selected' : '' ?>>
                                                <?= esc($city['city_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password Generation</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="password_type" 
                                               id="auto_password" value="auto" checked onchange="togglePasswordField()">
                                        <label class="form-check-label" for="auto_password">
                                            Auto Generate Password
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="password_type" 
                                               id="manual_password" value="manual" onchange="togglePasswordField()">
                                        <label class="form-check-label" for="manual_password">
                                            Set Manual Password
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="manual_password_field" style="display: none;">
                                    <label for="manual_password_input" class="form-label">Manual Password</label>
                                    <input type="password" class="form-control bg-dark text-white border-secondary" 
                                           id="manual_password_input" name="manual_password" 
                                           placeholder="Enter password (min 6 characters)">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/admin/trial-managers" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Trial Manager
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordField() {
    const autoRadio = document.getElementById('auto_password');
    const manualField = document.getElementById('manual_password_field');
    const manualInput = document.getElementById('manual_password_input');
    
    if (autoRadio.checked) {
        manualField.style.display = 'none';
        manualInput.removeAttribute('required');
    } else {
        manualField.style.display = 'block';
        manualInput.setAttribute('required', 'required');
    }
}
</script>
<?= $this->endSection() ?>
