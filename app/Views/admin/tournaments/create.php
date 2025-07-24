
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h5 class="card-title text-warning mb-0">
            <i class="fas fa-plus me-2"></i>Create New Tournament
        </h5>
        <a href="<?= base_url('admin/tournaments') ?>" class="btn btn-outline-warning">
            <i class="fas fa-arrow-left me-2"></i>Back to Tournaments
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-trophy me-2"></i>Tournament Details</h6>
            </div>
            <div class="card-body">
                <?= form_open('admin/tournaments/store', ['class' => 'needs-validation', 'novalidate' => true]) ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label text-light">Tournament Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" 
                               id="name" name="name" value="<?= old('name') ?>" required>
                        <div class="invalid-feedback">Please provide a tournament name.</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label text-light">Description</label>
                        <textarea class="form-control bg-dark text-light border-secondary" 
                                  id="description" name="description" rows="3"><?= old('description') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label text-light">Tournament Type <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark text-light border-secondary" 
                                    id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="knockout" <?= old('type') === 'knockout' ? 'selected' : '' ?>>Knockout (Elimination)</option>
                                <option value="round_robin" <?= old('type') === 'round_robin' ? 'selected' : '' ?>>Round Robin</option>
                            </select>
                            <div class="invalid-feedback">Please select a tournament type.</div>
                            <small class="text-muted">Knockout: 16 teams → 8 → 4 → 2 → Winner</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label text-light">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-dark text-light border-secondary" 
                                   id="start_date" name="start_date" value="<?= old('start_date') ?>" required>
                            <div class="invalid-feedback">Please provide a start date.</div>
                        </div>
                    </div>

                    <div class="card bg-secondary mb-3">
                        <div class="card-body">
                            <h6 class="text-warning mb-2"><i class="fas fa-info-circle me-2"></i>Tournament Information</h6>
                            <ul class="text-light mb-0">
                                <li><strong>Knockout Tournament:</strong> Single elimination format with 16 teams</li>
                                <li><strong>Round 1:</strong> 16 teams → 8 matches → 8 winners</li>
                                <li><strong>Round 2:</strong> 8 teams → 4 matches → 4 winners (Semi-Finals)</li>
                                <li><strong>Round 3:</strong> 4 teams → 2 matches → 2 winners</li>
                                <li><strong>Round 4:</strong> 2 teams → 1 match → 1 winner (Final)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('admin/tournaments') ?>" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-plus me-2"></i>Create Tournament
                        </button>
                    </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?= $this->endSection(); ?>
