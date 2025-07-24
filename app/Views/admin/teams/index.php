
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h5 class="card-title text-warning mb-0">
            <i class="fas fa-users me-2"></i>Team Management
        </h5>
        <div>
            <span class="badge bg-info me-2">16 Teams</span>
            <span class="badge bg-success me-2">Available Players</span>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach ($teams as $index => $team): ?>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card bg-dark border-secondary h-100 team-card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        <?= esc($team['name']) ?>
                    </h6>
                    <span class="badge bg-<?= $team['status'] === 'active' ? 'success' : ($team['status'] === 'draft' ? 'warning' : 'danger') ?>">
                        <?= ucfirst($team['status']) ?>
                    </span>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <div class="team-info mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Players:</span>
                            <span class="badge bg-primary"><?= $team['player_count'] ?>/11</span>
                        </div>
                        
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?= ($team['player_count'] / 11) * 100 ?>%" 
                                 aria-valuenow="<?= $team['player_count'] ?>" 
                                 aria-valuemin="0" aria-valuemax="11">
                            </div>
                        </div>

                        <?php if (!empty($team['coach_name'])): ?>
                            <div class="mb-2">
                                <small class="text-muted">Coach:</small>
                                <span class="text-white"><?= esc($team['coach_name']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($team['description'])): ?>
                            <div class="mb-2">
                                <small class="text-muted"><?= esc($team['description']) ?></small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto">
                        <a href="<?= base_url('admin/teams/manage/' . $team['id']) ?>" 
                           class="btn btn-outline-warning btn-sm w-100">
                            <i class="fas fa-edit me-2"></i>Manage Team
                        </a>
                    </div>
                </div>

                <div class="card-footer bg-transparent border-secondary">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Updated: <?= date('M d, Y', strtotime($team['updated_at'])) ?>
                    </small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
.team-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    min-height: 280px;
}

.team-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.progress {
    background-color: rgba(255,255,255,0.1);
}

.card-footer {
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .col-xl-3, .col-lg-4, .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>

<?= $this->endSection(); ?>
