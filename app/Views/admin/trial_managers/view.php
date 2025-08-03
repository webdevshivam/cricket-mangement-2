
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Trial Manager Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Manager Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark text-light border-info">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-tie text-info"></i> <?= esc($manager['name']) ?>
                    </h4>
                    <div>
                        <a href="/admin/trial-managers/edit/<?= $manager['id'] ?>" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="/admin/trial-managers" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?= esc($manager['email']) ?></p>
                            <p><strong>Trial Name:</strong> <?= esc($manager['trial_name']) ?></p>
                            <p><strong>City:</strong> <?= esc($manager['city_name'] ?? 'Not Set') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?= $manager['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($manager['status']) ?>
                                </span>
                            </p>
                            <p><strong>Created:</strong> <?= date('M d, Y H:i', strtotime($manager['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['total_players'] ?></h3>
                    <p class="mb-0">Total Players</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['full_payment'] ?></h3>
                    <p class="mb-0">Full Payment</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['partial_payment'] ?></h3>
                    <p class="mb-0">Partial Payment</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['no_payment'] ?></h3>
                    <p class="mb-0">No Payment</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Summary -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>₹<?= number_format($stats['total_collection'], 2) ?></h4>
                    <p class="mb-0">Total Collection</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-dark border-primary text-light">
                <div class="card-body text-center">
                    <h4>₹<?= number_format($stats['online_collection'], 2) ?></h4>
                    <p class="mb-0">Online Collection</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-dark border-secondary text-light">
                <div class="card-body text-center">
                    <h4>₹<?= number_format($stats['offline_collection'], 2) ?></h4>
                    <p class="mb-0">Offline Collection</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Players List -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Assigned Players (<?= count($players) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Cricket Type</th>
                                    <th>Payment Status</th>
                                    <th>Registered</th>
                                    <th>Verified</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($players)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No players assigned to this trial manager</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($players as $player): ?>
                                        <tr>
                                            <td><?= esc($player['name']) ?></td>
                                            <td><?= esc($player['mobile']) ?></td>
                                            <td><?= esc($player['email'] ?? 'N/A') ?></td>
                                            <td><?= esc($player['age']) ?></td>
                                            <td><?= esc(ucfirst($player['cricket_type'])) ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = 'secondary';
                                                switch ($player['payment_status']) {
                                                    case 'full': $badgeClass = 'success'; break;
                                                    case 'partial': $badgeClass = 'warning'; break;
                                                    case 'no_payment': $badgeClass = 'danger'; break;
                                                }
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $player['payment_status'])) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($player['created_at'])) ?></td>
                                            <td>
                                                <?php if ($player['verified_at']): ?>
                                                    <span class="badge bg-success">Verified</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
