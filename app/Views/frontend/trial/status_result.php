
<?= $this->extend('layouts/frontend') ?>

<?= $this->section('title') ?>Trial Status Result<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-success text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-user-check me-2"></i>
                            Trial Status Found
                        </h3>
                    </div>
                    
                    <div class="card-body p-5">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group mb-3">
                                    <label class="fw-bold text-muted">Player Name:</label>
                                    <p class="fs-5 mb-0"><?= esc($player['name']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group mb-3">
                                    <label class="fw-bold text-muted">Mobile Number:</label>
                                    <p class="fs-5 mb-0"><?= esc($player['mobile']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group mb-3">
                                    <label class="fw-bold text-muted">Age:</label>
                                    <p class="fs-5 mb-0"><?= esc($player['age']) ?> years</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group mb-3">
                                    <label class="fw-bold text-muted">Cricket Type:</label>
                                    <p class="fs-5 mb-0"><?= esc($player['cricket_type']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group mb-3">
                                    <label class="fw-bold text-muted">Payment Status:</label>
                                    <?php 
                                    $statusClass = 'danger';
                                    $statusText = 'No Payment';
                                    
                                    switch($player['payment_status']) {
                                        case 'partial':
                                            $statusClass = 'warning';
                                            $statusText = 'Partial Payment';
                                            break;
                                        case 'full':
                                            $statusClass = 'success';
                                            $statusText = 'Full Payment';
                                            break;
                                    }
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?> fs-6"><?= $statusText ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group mb-3">
                                    <label class="fw-bold text-muted">Trial Completed:</label>
                                    <span class="badge bg-<?= $player['trial_completed'] ? 'success' : 'secondary' ?> fs-6">
                                        <?= $player['trial_completed'] ? 'Yes' : 'Pending' ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if ($grade): ?>
                            <div class="alert alert-success mt-4">
                                <h5 class="alert-heading">
                                    <i class="fas fa-trophy me-2"></i>
                                    Grade Assigned!
                                </h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Grade:</strong> <?= esc($grade['title']) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>League Fee:</strong> ₹<?= esc($grade['league_fee']) ?>
                                    </div>
                                </div>
                                <?php if (!empty($grade['description'])): ?>
                                    <div class="mt-2">
                                        <strong>Description:</strong> <?= esc($grade['description']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mt-4">
                                <h5 class="alert-heading">
                                    <i class="fas fa-clock me-2"></i>
                                    Grade Assignment Pending
                                </h5>
                                <p class="mb-0">Your grade will be assigned after trial evaluation. Please check back later.</p>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('trial-status') ?>" class="btn btn-primary me-3">
                                <i class="fas fa-search me-2"></i>
                                Check Another Status
                            </a>
                            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
