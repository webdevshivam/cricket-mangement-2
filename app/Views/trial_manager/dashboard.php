<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Trial Manager Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-2 text-warning">
                                <i class="fas fa-clipboard-check me-2"></i>
                                Welcome, <?= session()->get('name') ?>!
                            </h1>
                            <p class="mb-0 fs-5">Managing trials for: <strong class="text-warning"><?= session()->get('trial_name') ?></strong></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-futbol text-warning" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient text-white" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="card-body text-center">
                    <div class="stats-number fs-1 fw-bold"><?= $stats['total_players'] ?? 0 ?></div>
                    <div class="stats-label">
                        <i class="fas fa-users me-1"></i>Total Players
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient text-white" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-center">
                    <div class="stats-number fs-1 fw-bold"><?= $stats['full_payment'] ?? 0 ?></div>
                    <div class="stats-label">
                        <i class="fas fa-check-circle me-1"></i>Full Payment
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient text-white" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="card-body text-center">
                    <div class="stats-number fs-1 fw-bold"><?= $stats['partial_payment'] ?? 0 ?></div>
                    <div class="stats-label">
                        <i class="fas fa-exclamation-triangle me-1"></i>Partial Payment
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient text-white" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <div class="card-body text-center">
                    <div class="stats-number fs-1 fw-bold">₹<?= number_format($stats['total_collection'] ?? 0) ?></div>
                    <div class="stats-label">
                        <i class="fas fa-money-bill-wave me-1"></i>Total Collection
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-dark text-light border-warning h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-search text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="text-warning">Player Verification</h4>
                    <p class="text-light">Search and verify player registration status, collect payments</p>
                    <a href="/trial-manager/player-verification" class="btn btn-warning">
                        <i class="fas fa-search me-2"></i>Start Verification
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-dark text-light border-warning h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-bar text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="text-warning">Payment Reports</h4>
                    <p class="text-light">View detailed payment reports and collection summaries</p>
                    <button class="btn btn-warning" onclick="showPaymentModal()">
                        <i class="fas fa-chart-bar me-2"></i>View Reports
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Players -->
    <?php if (!empty($recent_players)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Players
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-hover">
                            <thead class="table-warning text-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Payment Status</th>
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_players as $player): ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-user me-2 text-warning"></i>
                                        <?= esc($player['name']) ?>
                                    </td>
                                    <td><?= esc($player['mobile']) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = match($player['payment_status']) {
                                            'full' => 'bg-success',
                                            'partial' => 'bg-warning text-dark',
                                            'no_payment' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        $statusText = match($player['payment_status']) {
                                            'full' => 'Full Payment',
                                            'partial' => 'Partial Payment',
                                            'no_payment' => 'No Payment',
                                            default => 'Unknown'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar me-1 text-warning"></i>
                                        <?= date('M d, Y', strtotime($player['created_at'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Payment Report Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Payment Collection Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Online Collection</h5>
                                <h3>₹<?= number_format($stats['online_collection'] ?? 0) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h5 class="card-title">Offline Collection</h5>
                                <h3>₹<?= number_format($stats['offline_collection'] ?? 0) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showPaymentModal() {
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}
</script>
<?= $this->endSection() ?>