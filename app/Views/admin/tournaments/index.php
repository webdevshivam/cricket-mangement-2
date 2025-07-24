
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h5 class="card-title text-warning mb-0">
            <i class="fas fa-trophy me-2"></i>Tournament Management
        </h5>
        <div>
            <a href="<?= base_url('admin/tournaments/create') ?>" class="btn btn-warning">
                <i class="fas fa-plus me-2"></i>Create Tournament
            </a>
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
    <?php if (empty($tournaments)): ?>
        <div class="col-12">
            <div class="card bg-dark border-secondary text-center py-5">
                <div class="card-body">
                    <i class="fas fa-trophy fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No tournaments created yet</h5>
                    <p class="text-muted">Create your first tournament to start managing matches and winners.</p>
                    <a href="<?= base_url('admin/tournaments/create') ?>" class="btn btn-warning">
                        <i class="fas fa-plus me-2"></i>Create First Tournament
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($tournaments as $tournament): ?>
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card bg-dark border-secondary h-100 tournament-card">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            <?= esc($tournament['name']) ?>
                        </h6>
                        <span class="badge bg-<?= $tournament['status'] === 'completed' ? 'success' : ($tournament['status'] === 'active' ? 'warning' : ($tournament['status'] === 'draft' ? 'info' : 'danger')) ?>">
                            <?= ucfirst($tournament['status']) ?>
                        </span>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="tournament-info mb-3">
                            <div class="mb-2">
                                <small class="text-muted">Type:</small>
                                <span class="text-white"><?= ucfirst($tournament['type']) ?></span>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">Current Round:</small>
                                <span class="badge bg-primary"><?= $tournament['current_round'] ?></span>
                            </div>

                            <?php if (!empty($tournament['description'])): ?>
                                <div class="mb-2">
                                    <small class="text-muted"><?= esc($tournament['description']) ?></small>
                                </div>
                            <?php endif; ?>

                            <?php if ($tournament['status'] === 'completed' && !empty($tournament['winner_team_id'])): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Winner:</small>
                                    <span class="text-success"><i class="fas fa-crown me-1"></i>Team Winner</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('admin/tournaments/manage/' . $tournament['id']) ?>" 
                                   class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-cog me-2"></i>Manage Matches
                                </a>
                                <a href="<?= base_url('admin/tournaments/bracket/' . $tournament['id']) ?>" 
                                   class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-sitemap me-2"></i>View Bracket
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-secondary">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Created: <?= date('M d, Y', strtotime($tournament['created_at'])) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.tournament-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    min-height: 300px;
}

.tournament-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.card-footer {
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .col-xl-4, .col-lg-6, .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>

<?= $this->endSection(); ?>
