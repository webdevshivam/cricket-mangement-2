<?= $this->extend('layouts/trial_manager') ?>

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
                                Welcome, <?= session()->get('tm_name') ?>!
                            </h1>
                            <p class="mb-0 fs-5">Managing trials for: <strong class="text-warning"><?= session()->get('tm_trial_name') ?></strong></p>
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
        <div class="col-md-4">
            <div class="card bg-dark text-light border-warning h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-plus text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="text-warning">Register New Player</h4>
                    <p class="text-light">Register new player with full payment collection</p>
                    <button class="btn btn-warning" onclick="showRegisterModal()">
                        <i class="fas fa-user-plus me-2"></i>Register Player
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
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
        <div class="col-md-4">
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

<!-- Register New Player Modal -->
<div class="modal fade" id="registerPlayerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Register New Player</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="registerPlayerForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-warning">Player Name *</label>
                                <input type="text" class="form-control bg-dark text-white border-warning" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-warning">Mobile Number *</label>
                                <input type="tel" class="form-control bg-dark text-white border-warning" name="mobile" pattern="[0-9]{10}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-warning">Email</label>
                                <input type="email" class="form-control bg-dark text-white border-warning" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-warning">Age *</label>
                                <input type="number" class="form-control bg-dark text-white border-warning" name="age" min="11" max="49" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-warning">Cricket Type *</label>
                                <select class="form-select bg-dark text-white border-warning" name="cricket_type" required>
                                    <option value="">Select Type</option>
                                    <option value="bowler">Bowler (₹999 + ₹199 = ₹1198)</option>
                                    <option value="batsman">Batsman (₹999 + ₹199 = ₹1198)</option>
                                    <option value="all-rounder">All-Rounder (₹1199 + ₹199 = ₹1398)</option>
                                    <option value="wicket-keeper">Wicket-Keeper (₹1199 + ₹199 = ₹1398)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-warning">Payment Type *</label>
                                <select class="form-select bg-dark text-white border-warning" name="payment_type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="offline">Cash Payment</option>
                                    <option value="online">UPI/Online Payment</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="registerNewPlayer()">
                    <i class="fas fa-user-plus me-2"></i>Register Player
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showPaymentModal() {
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

function showRegisterModal() {
    new bootstrap.Modal(document.getElementById('registerPlayerModal')).show();
}

function registerNewPlayer() {
    const formData = new FormData(document.getElementById('registerPlayerForm'));
    const data = Object.fromEntries(formData);

    // Validate required fields
    if (!data.name || !data.mobile || !data.age || !data.cricket_type || !data.payment_type) {
        alert('Please fill all required fields');
        return;
    }

    fetch('/trial-manager/register-player', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Player registered successfully!\n\nPayment Details:\nTrial Fees: ₹${data.payment_breakdown.trial_fees}\nT-Shirt Fees: ₹${data.payment_breakdown.tshirt_fees}\nTotal Collected: ₹${data.payment_breakdown.total}\nPayment Type: ${data.payment_type}`);
            bootstrap.Modal.getInstance(document.getElementById('registerPlayerModal')).hide();
            document.getElementById('registerPlayerForm').reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            alert(data.message || 'Failed to register player');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while registering player');
    });
}
</script>
<?= $this->endSection() ?>